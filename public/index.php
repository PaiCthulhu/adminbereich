<?php
define('ROOT', dirname(__DIR__));
if(strpos(str_replace(array('/', '\\'),'/', ROOT), $_SERVER["DOCUMENT_ROOT"]) === false)
    define('PATH', '');
else
    define('PATH', str_replace($_SERVER["DOCUMENT_ROOT"],"",str_replace(array('/', '\\'),'/', ROOT)));
define('HOST', $_SERVER['HTTP_HOST']);

if (version_compare(phpversion(), '7.0', '<'))
    echo 'O admBereich requer no mínimo a versão 7.0 do php, por favor, atualize';

require_once(ROOT.'/library/loader.php');