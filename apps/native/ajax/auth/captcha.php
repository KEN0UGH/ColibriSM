<?php
session_start();

$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$captcha_code = '';

for ($i = 0; $i < 6; $i++) {
    $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
}

$_SESSION['captcha']      = $captcha_code;
$_SESSION['captcha_time'] = time();

header('Content-Type: image/png');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$image = imagecreatetruecolor(120, 40);
$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 64, 64, 64);

imagefilledrectangle($image, 0, 0, 120, 40, $bg_color);

for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, 120), rand(0, 40), rand(0, 120), rand(0, 40), $line_color);
}

$font = 5;
for ($i = 0; $i < strlen($captcha_code); $i++) {
    imagestring($image, $font, 10 + ($i * 15), 10 + rand(-5, 5), $captcha_code[$i], $text_color);
}

imagepng($image);
imagedestroy($image);