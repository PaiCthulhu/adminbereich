<?php
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));
define('PATH', str_replace($_SERVER["DOCUMENT_ROOT"],"",str_replace(array('/', '\\'),'/', ROOT)));
define('HOST', $_SERVER['HTTP_HOST']);

if (version_compare(phpversion(), '7.0', '<'))
    echo 'O admBereich requer no mínimo a versão 7.0 do php, por favor, atualize';

require_once(ROOT.DS.'library'.DS.'loader.php');