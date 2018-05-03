<?php
namespace AdmBereich;

class Router{
    protected $routes;
    public $namespace;

    function __construct(){
        $this->namespace = 'AdmBereich';
    }

    /**
     * @param string $url
     */
    function route($url){
        $url = trim($url, '/\\');
        if(isset($this->routes[$url]))
            $params = explode('/', $this->routes[$url]);
        else{
            $params = explode('/', $url);
            if($params[0] == 'admin')
                array_shift($params);
        }

        $ex = $this->getNamespace().$params[0];

        if(class_exists($ex)){
            if(!isset($params[1]) || $params[1] == '')
                $params[1] = 'index';
            if(method_exists($ex, $params[1])){
                $o = new $ex();
                if(isset($params[2])){
                    array_shift($params);
                    $method = array_shift($params);
                    $o->{$method}(implode('/', $params));
                }
                else
                    $o->{$params[1]}();
            }
            else if(method_exists($ex, '__default')){
                $o = $this->getNamespace().array_shift($params);
                $o = new $o();
                $o->__default(implode('/', $params));
            }
            else{
                echo "Rota não existente: Método '{$params[1]}' não encontrado";
                dump($url);
                dump($_SERVER);
            }
        }
        else{
            echo "Rota não existente: Classe '{$params[0]}' não encontrada";
            dump($url);
            dump($_SERVER);
        }
    }

    function getNamespace(){
        return '\\'.$this->namespace.'\\';
    }

    /**
     * @param string $route
     * @param string $action
     */
    function get($route, $action){
        if(is_string($action)){
            $this->routes[$route] = $action;
        }
    }

    static function redirect($path){
        header("Location: ".PATH.DS.$path);
        die();
    }
}