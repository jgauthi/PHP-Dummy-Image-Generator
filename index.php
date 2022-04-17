<?php
/**
 * Dynamic Dummy Image Generator — as seen on DummyImage.com.
 *
 * This script enables you to create placeholder images in a breeze. Please refer to the README on how to use it.
 * (Original idea by Russel Heimlich. When I first published this script, DummyImage.com was not Open Source, so I had to write a small script to replace the function on my own server.)
 *
 * @author  Fabian Beiner <fb@fabianbeiner.de>
 * @contrib Jgauthi <github.com/jgauthi>
 * @license MIT
 * @see     https://github.com/jgauthi/PHP-Dummy-Image-Generator
 * @version 1.1
 */

// Config parameters
$cfgParameter = (!empty($_GET['cfg']) ? $_GET['cfg'] : 'default');
$config = parse_ini_file(__DIR__.'/config.ini', true);
$config = !empty($config[$cfgParameter]) ? $config[$cfgParameter] : $config['default'];

// Handle the “size” parameter
$size = $config['size'];
if (!empty($_GET['size'])) {
    $size = $_GET['size'];
}
list($imgWidth, $imgHeight) = explode('x', $size.'x');
if ('' === $imgHeight) {
    $imgHeight = $imgWidth;
}
$filterOptions = [
    'options' => [
        'min_range' => 0,
        'max_range' => 9999,
    ],
];
if (false === filter_var($imgWidth, FILTER_VALIDATE_INT, $filterOptions)) {
    $imgWidth = 640;
}
if (false === filter_var($imgHeight, FILTER_VALIDATE_INT, $filterOptions)) {
    $imgHeight = 480;
}

// Handle the “type” parameter
$type = $config['type'];
if (isset($_GET['type']) && in_array(strtolower($_GET['type']), ['png', 'gif', 'jpg', 'jpeg'])) {
    $type = strtolower($_GET['type']);
}

// Handle the “text” parameter
$text = str_replace(
    ['[WIDTH]', '[HEIGHT]', '[TYPE]'],
    [$imgWidth, $imgHeight, $type],
    $config['text_value']
);
if (isset($_GET['text']) && strlen($_GET['text'])) {
    $text = filter_var(trim($_GET['text']), FILTER_SANITIZE_STRING);
}
$encoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
if ('UTF-8' !== $encoding) {
    $text = mb_convert_encoding($text, 'UTF-8', $encoding);
}
$text = mb_encode_numericentity($text, [0x0, 0xFFFF, 0, 0xFFFF], 'UTF-8');

// Handle the “bg” parameter
$bg = $config['bg_color'];
if (isset($_GET['bg']) && (6 === strlen($_GET['bg']) || 3 === strlen($_GET['bg']))) {
    $bg = strtoupper($_GET['bg']);
    if (3 === strlen($_GET['bg'])) {
        $bg = strtoupper($_GET['bg'][0].
            $_GET['bg'][0].
            $_GET['bg'][1].
            $_GET['bg'][1].
            $_GET['bg'][2].
            $_GET['bg'][2]
        );
    }
}
list($bgRed, $bgGreen, $bgBlue) = sscanf($bg, '%02x%02x%02x');

// Handle the “color” parameter
$color = $config['text_color'];
if (isset($_GET['color']) && (6 === strlen($_GET['color']) || 3 === strlen($_GET['color']))) {
    $color = strtoupper($_GET['color']);
    if (3 === strlen($_GET['color'])) {
        $color = strtoupper($_GET['color'][0].
            $_GET['color'][0].
            $_GET['color'][1].
            $_GET['color'][1].
            $_GET['color'][2].
            $_GET['color'][2]
        );
    }
}
list($colorRed, $colorGreen, $colorBlue) = sscanf($color, '%02x%02x%02x');

// Define the typeface settings
$fontFile = 'arial';
if (!empty($config['font'])) {
    $fontConfigured = realpath(__DIR__).DIRECTORY_SEPARATOR.$config['font'];
    if (is_readable($fontConfigured)) {
        $fontFile = $fontConfigured;
    }
}

$fontSize = round(($imgWidth - 50) / 8);
if ($fontSize <= 9) {
    $fontSize = 9;
}

// Generate the image
$image = imagecreatetruecolor($imgWidth, $imgHeight);
$colorFill = imagecolorallocate($image, $colorRed, $colorGreen, $colorBlue);
$bgFill = imagecolorallocate($image, $bgRed, $bgGreen, $bgBlue);
imagefill($image, 0, 0, $bgFill);
$textBox = imagettfbbox($fontSize, 0, $fontFile, $text);

while ($textBox[4] >= $imgWidth) {
    $fontSize -= round($fontSize / 2);
    $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
    if ($fontSize <= 9) {
        $fontSize = 9;
        break;
    }
}
$textWidth = abs($textBox[4] - $textBox[0]);
$textHeight = abs($textBox[5] - $textBox[1]);
$textX = ($imgWidth - $textWidth) / 2;
$textY = ($imgHeight + $textHeight) / 2;
imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);

// Return the image and destroy it afterwards
header('HTTP/1.1 201 Created');
switch ($type) {
    case 'png':
        header('Content-Type: image/png');
        imagepng($image, null, 9);
        break;
    case 'gif':
        header('Content-Type: image/gif');
        imagegif($image);
        break;
    case 'jpg':
    case 'jpeg':
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        break;
}
imagedestroy($image);
