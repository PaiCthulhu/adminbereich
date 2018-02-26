<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));
define('PATH', str_replace($_SERVER["DOCUMENT_ROOT"],"",str_replace(array('/', '\\'),'/', ROOT)));

require_once(ROOT.DS.'library'.DS.'loader.php');