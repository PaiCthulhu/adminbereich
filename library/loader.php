<?php
require_once(ROOT.DS.'config'.DS.'config.php');
require ROOT.'/vendor/autoload.php';
require_once(ROOT.DS.'library'.DS.'shared.php');

require_once(ROOT.DS.'config'.DS.'routes.php');

if(DEBUG)
    \AdmBereich\Sass::compile(ROOT."/public/sass/", ROOT."/public/css/", '\Leafo\ScssPhp\Formatter\Compressed');

$routes->route($_GET['url']);