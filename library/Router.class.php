<?php
class Router{
    protected $routes;

    /**
     * @param string $url
     */
    function route($url){
        if(isset($this->routes[$url])){
            $params = explode('/', $this->routes[$url]);
        }
        else{
            echo "Rota não existente";
            dump($url);
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
}