<?php
/**
 * Modern HTML Parser - Wrapper around PHP's DOMDocument
 * Provides backward-compatible interface for existing code
 * 
 * Replaces: Simple HTML DOM Parser v1.9.1
 * Benefits: 
 * - Built-in PHP library (no external dependency)
 * - Better performance and memory efficiency
 * - More robust HTML parsing
 * - Better XPath support
 */

defined('DEFAULT_TARGET_CHARSET') || define('DEFAULT_TARGET_CHARSET', 'UTF-8');
defined('MAX_FILE_SIZE') || define('MAX_FILE_SIZE', 600000);

/**
 * Parse HTML from URL
 * @param string $url URL or file path
 * @param bool $use_include_path Use include path
 * @param resource $context Stream context
 * @param int $offset File offset
 * @param int $maxlen Maximum bytes to read
 * @return SimpleHTMLDOMNode|false
 */
function file_get_html(
    $url,
    $use_include_path = false,
    $context = null,
    $offset = 0,
    $maxlen = -1
) {
    if (!filter_var($url, FILTER_VALIDATE_URL) && !file_exists($url)) {
        return false;
    }
    
    try {
        // Create context if not provided
        if (null === $context) {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (compatible; PHP-Parser)\r\n",
                    'timeout' => 10
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);
        }
        
        $maxlen = ($maxlen <= 0) ? MAX_FILE_SIZE : $maxlen;
        $content = @file_get_contents($url, $use_include_path, $context, $offset, $maxlen);
        
        if ($content === false) {
            return false;
        }
        
        if (strlen($content) > MAX_FILE_SIZE) {
            $content = substr($content, 0, MAX_FILE_SIZE);
        }
        
        return str_get_html($content);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Parse HTML from string
 * @param string $str HTML string
 * @return SimpleHTMLDOMNode|false
 */
function str_get_html($str) {
    if (empty($str)) {
        return false;
    }
    
    try {
        $dom = new DOMDocument();
        $dom->encoding = DEFAULT_TARGET_CHARSET;
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="' . DEFAULT_TARGET_CHARSET . '">' . $str);
        libxml_clear_errors();
        
        return new SimpleHTMLDOMNode($dom);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Simple HTML DOM Node wrapper
 * Provides jQuery-like CSS selector interface over DOMDocument
 */
class SimpleHTMLDOMNode {
    protected $dom;
    protected $node;
    protected $_charset = 'UTF-8';
    
    public function __construct($domNode = null) {
        if ($domNode instanceof DOMDocument) {
            $this->dom = $domNode;
            $this->node = $domNode->documentElement;
        } elseif ($domNode instanceof DOMElement) {
            $this->node = $domNode;
            $this->dom = $domNode->ownerDocument;
        } elseif ($domNode instanceof SimpleHTMLDOMNode) {
            $this->node = $domNode->node;
            $this->dom = $domNode->dom;
        } else {
            $this->dom = new DOMDocument();
            $this->node = null;
        }
    }
    
    /**
     * Find elements by CSS selector
     * @param string $selector CSS selector
     * @param int|null $index Return specific index or all if null
     * @return SimpleHTMLDOMNode|array|null
     */
    public function find($selector, $index = null) {
        if (!$this->node) {
            return null;
        }
        
        try {
            $xpath = new DOMXPath($this->dom);
            $xpathQuery = $this->cssToXpath($selector);
            
            if (!$xpathQuery) {
                return null;
            }
            
            $nodeList = $xpath->query($xpathQuery, $this->node);
            
            if (!$nodeList || $nodeList->length === 0) {
                return null;
            }
            
            if ($index !== null) {
                if ($index < 0) {
                    $index = $nodeList->length + $index;
                }
                if ($index >= $nodeList->length) {
                    return null;
                }
                return new SimpleHTMLDOMNode($nodeList->item($index));
            }
            
            // Return array of nodes
            $results = [];
            foreach ($nodeList as $node) {
                $results[] = new SimpleHTMLDOMNode($node);
            }
            return $results;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get property value
     */
    public function __get($name) {
        if ($name === 'plaintext' || $name === 'text') {
            if ($this->node instanceof DOMDocument) {
                return $this->node->documentElement->nodeValue;
            }
            return $this->node ? $this->node->nodeValue : '';
        }
        
        if ($name === 'content') {
            return $this->getAttribute('content');
        }
        
        if ($name === 'innertext') {
            return $this->getInnerHTML();
        }
        
        if ($name === 'outertext') {
            return $this->getOuterHTML();
        }
        
        if ($name === 'tag') {
            return $this->node ? $this->node->nodeName : '';
        }
        
        return $this->getAttribute($name);
    }
    
    /**
     * Get attribute value
     * @param string $name
     * @return string|null
     */
    public function getAttribute($name) {
        if (!$this->node instanceof DOMElement) {
            return null;
        }
        
        $value = $this->node->getAttribute($name);
        return $value !== '' ? $value : null;
    }
    
    /**
     * Get inner HTML
     */
    protected function getInnerHTML() {
        if (!$this->node) {
            return '';
        }
        
        $html = '';
        foreach ($this->node->childNodes as $child) {
            $html .= $this->dom->saveHTML($child);
        }
        return $html;
    }
    
    /**
     * Get outer HTML
     */
    protected function getOuterHTML() {
        if (!$this->node) {
            return '';
        }
        
        if ($this->node instanceof DOMDocument) {
            return $this->dom->saveHTML();
        }
        
        return $this->dom->saveHTML($this->node);
    }
    
    /**
     * Convert CSS selector to XPath
     * Supports: element, .class, #id, [attribute], [attribute='value']
     */
    protected function cssToXpath($selector) {
        $selector = trim($selector);
        
        if (empty($selector)) {
            return false;
        }
        
        // Handle multiple selectors (comma-separated)
        if (strpos($selector, ',') !== false) {
            $selectors = array_map('trim', explode(',', $selector));
            $xpaths = [];
            foreach ($selectors as $sel) {
                $xpath = $this->cssToXpath($sel);
                if ($xpath) {
                    $xpaths[] = $xpath;
                }
            }
            return count($xpaths) > 0 ? implode(' | ', $xpaths) : false;
        }
        
        // Descendant selector with space
        if (preg_match('/^([^\[]+)\s+([^\[]+)$/', $selector, $matches) && 
            strpos($matches[1], '[') === false && 
            strpos($matches[2], '[') === false) {
            $part1 = $this->cssToXpath(trim($matches[1]));
            $part2 = $this->cssToXpath(trim($matches[2]));
            if ($part1 && $part2) {
                return $part1 . '//' . $part2;
            }
        }
        
        // ID selector
        if ($selector[0] === '#') {
            $id = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($selector, 1));
            return "//*[@id='" . $id . "']";
        }
        
        // Class selector
        if ($selector[0] === '.') {
            $class = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($selector, 1));
            return "//*[contains(concat(' ', @class, ' '), ' " . $class . " ')]";
        }
        
        // Attribute selector: [attr] or [attr='value']
        if (preg_match('/^([^\[]*)\[([^\]]+)\]$/', $selector, $matches)) {
            $element = $matches[1] ?: '*';
            $attr = $matches[2];
            
            if (strpos($attr, '=') !== false) {
                list($attrName, $attrValue) = array_map('trim', explode('=', $attr, 2));
                $attrValue = trim($attrValue, '\'"');
                return "//{$element}[@{$attrName}='{$attrValue}']";
            }
            
            return "//{$element}[@{$attr}]";
        }
        
        // Tag selector
        if (preg_match('/^[a-z0-9\-]+$/i', $selector)) {
            return "//{$selector}";
        }
        
        // Complex selectors: tag.class#id[attr]
        if (preg_match('/^([a-z0-9\-]*)((?:\.[a-z0-9\-]+)*)(#[a-z0-9\-]+)?((?:\[[^\]]+\])*)$/i', 
            $selector, $matches)) {
            $element = $matches[1] ?: '*';
            $classes = $matches[2];
            $id = $matches[3];
            $attrs = $matches[4];
            
            $xpath = "//{$element}";
            
            if ($id) {
                $id = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($id, 1));
                $xpath .= "[@id='{$id}']";
            }
            
            if ($classes) {
                $classArray = array_map(function($c) {
                    return preg_replace('/[^a-zA-Z0-9_-]/', '', substr($c, 1));
                }, explode('.', $classes));
                foreach ($classArray as $class) {
                    $xpath .= "[contains(concat(' ', @class, ' '), ' " . $class . " ')]";
                }
            }
            
            if ($attrs) {
                preg_match_all('/\[([^\]]+)\]/', $attrs, $attrMatches);
                foreach ($attrMatches[1] as $attr) {
                    if (strpos($attr, '=') !== false) {
                        list($attrName, $attrValue) = array_map('trim', explode('=', $attr, 2));
                        $attrValue = trim($attrValue, '\'"');
                        $xpath .= "[@{$attrName}='{$attrValue}']";
                    } else {
                        $xpath .= "[@{$attr}]";
                    }
                }
            }
            
            return $xpath;
        }
        
        return false;
    }
    
    /**
     * Get children
     */
    public function children($idx = -1) {
        if (!$this->node) {
            return null;
        }
        
        $children = [];
        foreach ($this->node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $children[] = new SimpleHTMLDOMNode($child);
            }
        }
        
        if ($idx === -1) {
            return $children;
        }
        
        if ($idx < 0) {
            $idx = count($children) + $idx;
        }
        
        return isset($children[$idx]) ? $children[$idx] : null;
    }
    
    /**
     * Get first child
     */
    public function first_child() {
        $children = $this->children();
        return count($children) > 0 ? $children[0] : null;
    }
    
    /**
     * Get last child
     */
    public function last_child() {
        $children = $this->children();
        return count($children) > 0 ? $children[count($children) - 1] : null;
    }
    
    /**
     * Get next sibling
     */
    public function next_sibling() {
        if (!$this->node || !$this->node->parentNode) {
            return null;
        }
        
        $next = $this->node->nextSibling;
        while ($next && !($next instanceof DOMElement)) {
            $next = $next->nextSibling;
        }
        
        return $next ? new SimpleHTMLDOMNode($next) : null;
    }
    
    /**
     * Get previous sibling
     */
    public function prev_sibling() {
        if (!$this->node || !$this->node->parentNode) {
            return null;
        }
        
        $prev = $this->node->previousSibling;
        while ($prev && !($prev instanceof DOMElement)) {
            $prev = $prev->previousSibling;
        }
        
        return $prev ? new SimpleHTMLDOMNode($prev) : null;
    }
    
    /**
     * Get parent
     */
    public function parent() {
        if (!$this->node || !$this->node->parentNode) {
            return null;
        }
        
        return new SimpleHTMLDOMNode($this->node->parentNode);
    }
    
    /**
     * Check if has children
     */
    public function has_child() {
        return $this->node && $this->node->hasChildNodes();
    }
    
    /**
     * Get inner text
     */
    public function innertext() {
        return $this->getInnerHTML();
    }
    
    /**
     * Get outer text
     */
    public function outertext() {
        return $this->getOuterHTML();
    }
    
    /**
     * Get plain text
     */
    public function text() {
        if (!$this->node) {
            return '';
        }
        
        $text = '';
        foreach ($this->node->childNodes as $child) {
            if ($child instanceof DOMText) {
                $text .= $child->nodeValue;
            } elseif ($child instanceof DOMElement) {
                $childNode = new SimpleHTMLDOMNode($child);
                $text .= $childNode->text();
            }
        }
        
        return trim($text);
    }
    
    /**
     * Set/get attribute
     */
    public function __set($name, $value) {
        if ($this->node instanceof DOMElement) {
            if ($value === null || $value === false) {
                $this->node->removeAttribute($name);
            } else {
                $this->node->setAttribute($name, $value);
            }
        }
    }
    
    /**
     * Check if attribute exists
     */
    public function __isset($name) {
        if ($this->node instanceof DOMElement) {
            return $this->node->hasAttribute($name);
        }
        return false;
    }
    
    /**
     * Unset attribute
     */
    public function __unset($name) {
        if ($this->node instanceof DOMElement) {
            $this->node->removeAttribute($name);
        }
    }
    
    /**
     * Magic toString
     */
    public function __toString() {
        return $this->outertext();
    }
}
