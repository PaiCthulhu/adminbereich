<?php
class Router{
    protected $routes;

    /**
     * @param string $url
     */
    function route($url){
        $url = trim($url, '/\\');
        if(isset($this->routes[$url])){
            $params = explode('/', $this->routes[$url]);
            if(class_exists($params[0])){
                if(method_exists($params[0], $params[1])){
                    $o = new $params[0]();
                    $o->{$params[1]}();
                }
            }
        }
        else{
            echo "Rota não existente";
            dump($url);
            dump($_SERVER);
        }
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