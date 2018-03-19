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

/**
 * @param string $class Nome da Classe
 * @throws Exception Caso não encontre a classe, gera uma exceção
 */
function __autoload($class) {
    if (file_exists(ROOT . DS . 'library' . DS . $class . '.class.php')) {
        require_once(ROOT . DS . 'library' . DS . $class . '.class.php');
    }
    else if (file_exists(ROOT . DS . 'app' . DS . 'controllers' . DS . ucfirst($class) . '.php')) {
        require_once(ROOT . DS . 'app' . DS . 'controllers' . DS . ucfirst($class) . '.php');
    }
    else if (file_exists(ROOT . DS . 'app' . DS . 'models' . DS . $class . '.php')) {
        require_once(ROOT . DS . 'app' . DS . 'models' . DS . $class . '.php');
    }
    else if (file_exists(ROOT . DS . 'app' . DS . 'library' . DS . $class . '.php')) {
        require_once(ROOT . DS . 'app' . DS . 'library' . DS . $class . '.php');
    }
    else if(file_exists(ROOT.DS.'vendor'.DS.$class.DS.$class.'.php')){
        require_once(ROOT.DS.'vendor'.DS.$class.DS.$class.'.php');
    }
    else {
        throw new Exception('Classe "'.$class.'" não encontrada!');
    }
}

function exception_handler($exception) {
    $erro = new Controller();
    $erro->render('pages.404', ['erro'=>$exception->getMessage(),'dump'=>$exception]);
    /*echo "Erro: " , $exception->getMessage(), "\n";
    dump($exception);*/
}

set_exception_handler('exception_handler');

/**------------------------------------------------------------------------------------------------------------------**/

setReporting();