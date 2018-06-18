<?php
date_default_timezone_set('America/Sao_Paulo');

/**
 * @param string $class Nome da Classe
 * @throws \Exception Caso não encontre a classe, gera uma exceção
 */
spl_autoload_register(function ($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DS, $namespace) . DS;
    }
    $fileName .= str_replace('_', DS, $className) . '.php';

    $folder = ROOT.DS;
    if($namespace == DEFAULT_NAMESPACE || in_array($namespace, DEFAULT_LIBRARIES)){
        $file = str_replace('_', DS, $className) . '.php';
        if (file_exists(ROOT . DS . 'app/controllers/'.$file)){
            $fileName = $file;
            $folder .= 'app/controllers';
        }
        else if (file_exists(ROOT . DS . 'app/models/'.$file)){
            $fileName = $file;
            $folder .= 'app/models';
        }
        else{
            $folder .= 'app/library';
            //Check Trait
            $file = str_replace('.php', '.trait.php', $fileName);
            if (file_exists($folder.DS.$file))
                $fileName = $file;
            //Check Abstract
            $file = str_replace('.php', '.class.php', $fileName);
            if (file_exists($folder.DS.$file))
                $fileName = $file;
        }
    }
    else
        if($namespace == '')
            $folder .= 'vendor'.DS.strtolower($className);
    else
        $folder .= 'vendor';

    if (file_exists($folder.DS.$fileName))
        require_once($folder.DS.$fileName);
    else {
        dump([$className, $namespace, $fileName]);
        echo "<h2>Falha ao carregar a classe \"{$className}\": Arquivo {$folder}/{$fileName} não encontrado</h2>";
        echo "<pre>";
        debug_print_backtrace();
        echo "</pre>";
    }
});

function setReporting(){
    if(DEBUG == true){
        error_reporting(E_ALL);
        ini_set('display_errors',"on");
    }
    else{
        error_reporting(E_ALL);
        ini_set('display_errors',"off");
        ini_set('log_errors','On');
        ini_set('error_log',ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
    }
}

/**
 * Replace the last occurrance of the search string with the replacement string
 * @link https://stackoverflow.com/questions/3835636/php-replace-last-occurrence-of-a-string-in-a-string
 * @param string $search The value being searched for, otherwise known as the needle.
 * @param string $replace The replacement value that replaces found search
 * @param string $subject The string being searched and replaced on, otherwise known as the haystack.
 * @return string This function returns a string with the replaced values.
 */
function str_lreplace($search, $replace, $subject) {
    $pos = strrpos($subject, $search);
    if($pos !== false)
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    return $subject;
}

/**
 * Função para despuração de variáveis
 * @param mixed $var
 */
function dump($var){
    if(class_exists('Kint')){
        Kint::$aliases[] = 'dump';
        Kint::$aliases[] = 'exception_handler';
        Kint::dump($var);
    }
    else
        var_dump($var);
}


set_exception_handler(function ($exception) {
    $c = '\\'.DEFAULT_NAMESPACE.'\\'.MAIN_CLASS;
    $c = new $c();
    $c->_error($exception);
});

/**------------------------------------------------------------------------------------------------------------------**/

setReporting();