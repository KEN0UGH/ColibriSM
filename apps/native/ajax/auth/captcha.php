<?php
session_start();

// Check if a new CAPTCHA is requested
if (empty($_SESSION['captcha_time']) || (time() - $_SESSION['captcha_time']) > 3600) {
    // Generate new CAPTCHA every hour or on first request
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $captcha_code = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
    }
    $_SESSION['captcha'] = $captcha_code;
    $_SESSION['captcha_time'] = time();
} else {
    $captcha_code = $_SESSION['captcha'];
}

// Create image
$image = imagecreatetruecolor(120, 40);
$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 64, 64, 64);

// Fill background
imagefilledrectangle($image, 0, 0, 120, 40, $bg_color);

// Add noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $line_color);
}

// Add distorted text
$font = 5;
for ($i = 0; $i < strlen($captcha_code); $i++) {
    imagestring($image, $font, 10 + ($i * 15), 10 + rand(-5, 5), $captcha_code[$i], $text_color);
}

// Output image
header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');
imagepng($image);
imagedestroy($image);
?>