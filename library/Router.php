<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich;

/**
 * Responsável por rotear as urls para os controladores
 * @package AdmBereich
 */
class Router{
    /**
     * @var string $url Current requested URL
     */
    static public $url;
    /**
     * @var array $routes Lista das rotas adicionadas manualmente
     */
    protected $routes;
    /**
     * Nome do "namespace" dos controladores e modelos a serem roteados
     * @var string $namespace
     */
    public $namespace;
    /**
     * @var array $extraNamespaces Lista de namespaces extras
     */
    protected $extraNamespaces;

    /**
     * Construtor da classe Router
     */
    function __construct(){
        $this->namespace = 'AdmBereich';
    }

    /**
     * Recebe um url amigável e então busca uma ação correspondente entre os controladores disponíveis e as rotas
     * registradas manualmente
     * @param string $url O "path" da url enviada pela requisição
     * @throws \Exception
     */
    function route($url){
        self::$url = $url;
        $main = false;
        $namespace = $this->getNamespace().'Controllers\\';

        $route = \parse_url($url);
        if(!isset($route['path']) || empty($route['path']))
            $class = MAIN_CLASS;
        else{
            $path = $route['path'];
            if(isset($this->routes[trim($path, '/')]))
                $path = preg_split('@/@', $this->routes[trim($path, '/')], -1, PREG_SPLIT_NO_EMPTY);
            else{
                $path = preg_split('@/@', $path, -1, PREG_SPLIT_NO_EMPTY);
                if(isset($this->extraNamespaces[$path[0]])){
                    $namespace = $this->extraNamespaces[$path[0]];
                    array_shift($path);
                }
            }
            @list($class, $method, $params) = $path;
        }

        if(class_exists($namespace.ucfirst($class)))
            $class = $namespace.ucfirst($class);
        else {
            $params = $method;
            $method = $class;
            $class = $namespace.ucfirst(MAIN_CLASS);
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
            if($_ENV['APP_DEBUG']){
                if(!$main)
                    echo "Rota não existente: Método '{$method}' não encontrado";
                else
                    echo "Rota não existente: Classe '{$class}' não encontrada";

                dump($url);
                dump($_SERVER);
            }
            else{
                throw new \Exception("Rota \"/$url\" inválida!");
            }


        }

    }

    /**
     * Obtém o nome do namespace padrão das rotas
     * @return string
     */
    function getNamespace(){
        return '\\'.$this->namespace.'\\';
    }

    function addExtraNamespace($key, $namespace){
        $this->extraNamespaces[$key] = $namespace;
        return $this;
    }

    /**
     * Adiciona manualmente uma rota e sua ação correspondente para a lista
     * @param string $route
     * @param string $action
     */
    function get($route, $action){
        if(is_string($action)){
            $this->routes[$route] = $action;
        }
    }


    /**
     * Esboço
     * @todo Criar uma função que realmente leia o método http e então envia pro lugar certo
     * @param string|array $method Método de requisição HTTP. Valores possíveis incluem GET, POST, PUT, DELETE, HEAD,
     * OPTIONS, CONNECT, TRACE e PATCH
     * @param string $pattern Regex de reconhecimento da rota
     * @param string $action Ação a ser executada
     */
    public function map($method, $pattern, $action){

    }


    /**
     * Função de atalho para o preenchimento do cabeçalho de redirecionamento
     * @param $path
     */
    static function redirect($path){
        header("Location: ".PATH."/".$path);
        die();
    }
}