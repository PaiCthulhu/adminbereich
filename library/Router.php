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
        $main = false;

        if(isset($this->routes[trim($url, '/')]))
            @list($class, $method) = @explode('/', $this->routes[trim($url, '/')]);
        else{
            $route = explode('/', $url, 4);
            if($route[0] == 'admin')
                array_shift($route);
            @list($class, $method, $params) = $route;
        }

        if(class_exists($this->getNamespace().'Controllers\\'.ucfirst($class)))
            $class = $this->getNamespace().'Controllers\\'.ucfirst($class);
        else {
            $params = $method;
            $method = $class;
            $class = $this->getNamespace().'Controllers\\'.ucfirst(MAIN_CLASS);
            $main = true;
        }

        $method = (!isset($method) || $method == '')? 'index': $method;

        if(method_exists($class, $method)){
            $o = new $class();
            if(isset($params)){
                $o->{$method}($params);
            }
            else
                $o->{$method}();
        }
        else if(method_exists($class, '__default')){
            $o = new $class();
            $o->__default($method);
        }
        else{
            if(!$main)
                echo "Rota não existente: Método '{$method}' não encontrado";
            else
                echo "Rota não existente: Classe '{$class}' não encontrada";

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