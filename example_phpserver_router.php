<?php
const ROUTER_EXPREG = '#^(/images/)(film)?/?#i';
const FULLPATH_DUMMY_IMG_GENERATOR = '/path/to/PHP-Dummy-Image-Generator/index.php';

if (preg_match(ROUTER_EXPREG, $_SERVER['REQUEST_URI'], $extract)) {
    $_GET['cfg'] = $extract[2];
    // var_dump($extract);return true; // debug
    require_once FULLPATH_DUMMY_IMG_GENERATOR;
    return true;

} elseif (preg_match('#\.(?:png|jpe?g|gif|js|css|mp3|ogg|mp4|avi|webm|webp)$#', $_SERVER['REQUEST_URI'])) {
    return false;
} else {
    require_once __DIR__.'/index.php';
}
