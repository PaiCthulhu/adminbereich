<?php
class Router{
    protected $routes;

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

        if(class_exists($params[0])){
            if(!isset($params[1]) || $params[1] == '')
                $params[1] = 'index';
            if(method_exists($params[0], $params[1])){
                $o = new $params[0]();
                if(isset($params[2]))
                    $o->{$params[1]}($params[2]);
                else
                    $o->{$params[1]}();
            }
            else{
                echo "Rota não existente: Método '{$params[1]}' não encontrado";
                dump($url);
                dump($_SERVER);
            }
        }
        else{


            echo "Rota não existente: Classe '{$params[1]}' não encontrada";
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