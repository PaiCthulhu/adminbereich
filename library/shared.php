<?php
date_default_timezone_set('America/Sao_Paulo');

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
function dump(...$var){
    if(count($var) == 1)
        $var = $var[0];
    if(class_exists('Kint')){
        Kint::$aliases[] = 'dump';
        Kint::$aliases[] = 'exception_handler';
        Kint::dump($var);
    }
    else
        var_dump($var);
}


set_exception_handler(function ($exception) {
    $c = '\\'.DEFAULT_NAMESPACE.'\\Controllers\\'.MAIN_CLASS;
    $c = new $c();
    $c->_error($exception);
});

/**------------------------------------------------------------------------------------------------------------------**/

setReporting();