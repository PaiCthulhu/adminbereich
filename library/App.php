<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich;

use Dotenv\Dotenv;

/**
 * Class App
 * @package AdmBereich
 */
class App {

    /**
     * @var Router $router
     */
    protected $router;

    function __construct()
    {
        self::loadEnv();
    }

    function setRouter(Router $router){
        $this->router = $router;
        return $this;
    }

    function run(){
        $path = \ltrim(\str_freplace(PATH."/", '', $_SERVER['REQUEST_URI']), '/');
        $this->router->route($path);
    }

    static function loadEnv(){
        $dotenv = Dotenv::createImmutable(ROOT);
        $dotenv->load();
    }

    static function resourcePath($resource){
        $path = PATH."/";
        if(strpos($_SERVER['PHP_SELF'], "public") != false)
            $path .= "public/";
        return $path.$resource;
    }

}