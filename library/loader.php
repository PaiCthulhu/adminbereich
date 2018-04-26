<?php
require_once(ROOT.DS.'config'.DS.'config.php');
require_once(ROOT.DS.'library'.DS.'shared.php');

require_once(ROOT.DS.'vendor'.DS.'BladeOne'.DS.'BladeOne.php');

require_once(ROOT.DS.'config'.DS.'routes.php');

if(DEBUG)
    SassCompiler::run(ROOT."/public/sass/", ROOT."/public/css/", '\Leafo\ScssPhp\Formatter\Compressed');

$routes->route($_GET['url']);