<?php 
# @*************************************************************************@
# @ Software author: Mansur Terla (Mansur_TL)                               @
# @ UI/UX Designer & Web developer ;)                                       @
# @                                                                         @
# @*************************************************************************@
# @ Instagram: https://www.instagram.com/mansur_tl                          @
# @ VK: https://vk.com/mansur_tl_uiux                                       @
# @ Envato: http://codecanyon.net/user/mansur_tl                            @
# @ Behance: https://www.behance.net/mansur_tl                              @
# @ Telegram: https://t.me/mansurtl_contact                                 @
# @*************************************************************************@
# @ E-mail: mansurtl.contact@gmail.com                                      @
# @ Website: https://www.mansurtl.com                                       @
# @*************************************************************************@
# @ ColibriSM - The Ultimate Social Network PHP Script                      @
# @ Copyright (c)  ColibriSM. All rights reserved                           @
# @*************************************************************************@

function cl_admin_get_text_excerpt($text = '', $limit = 150) {
    $text = (string) $text;
    $text = stripcslashes($text);
    $text = htmlspecialchars_decode($text, ENT_QUOTES);
    $text = strip_tags($text);
    $text = trim(preg_replace('/\s+/', ' ', $text));

    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strlen')) {
        if (mb_strlen($text) > $limit) {
            $text = mb_substr($text, 0, $limit) . '...';
        }
    }
    else if (strlen($text) > $limit) {
        $text = substr($text, 0, $limit) . '...';
    }

    return $text;
}

function cl_admin_get_posts($args = array()) {
    global $cl, $me, $db;

    $args           = (is_array($args)) ? $args : array();
    $options        = array(
        "offset"    => false,
        "limit"     => 10,
        "offset_to" => false,
        "order"     => 'DESC',
        "keyword"   => false,
    );

    $args           =  array_merge($options, $args);
    $offset         =  $args['offset'];
    $limit          =  $args['limit'];
    $order          =  $args['order'];
    $keyword        =  $args['keyword'];
    $offset_to      =  $args['offset_to'];
    $data           =  array();
    $t_pubs         =  T_PUBS;
    $sql            =  cl_sqltepmlate('apps/cpanel/posts/sql/fetch_posts',array(
        'offset'    => $offset,
        't_pubs'    => $t_pubs,
        'limit'     => $limit,
        'offset_to' => $offset_to,
        'order'     => $order,
        'keyword'   => $keyword,
    ));

    $data  = array();
    $posts = $db->rawQuery($sql);

    if (cl_queryset($posts)) {
        foreach ($posts as $row) {
            $row['url']           = cl_link(cl_strf('thread/%d', $row['id']));
            $row['replys_count']  = cl_number($row['replys_count']);
            $row['reposts_count'] = cl_number($row['reposts_count']);
            $row['likes_count']   = cl_number($row['likes_count']);
            $row['time']          = date('d M, Y h:m',$row['time']);
            $row['text_excerpt']  = cl_admin_get_text_excerpt($row['text'], 150);
            $data[]               = $row;
        }
    }
    
    return $data;
}