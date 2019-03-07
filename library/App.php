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
 * Class App
 * @package AdmBereich
 */
class App {

    /**
     * @var Router $router
     */
    protected $router;

    function setRouter(Router $router){
        $this->router = $router;
        return $this;
    }

    function run(){
        $path = ltrim(str_replace(PATH, '', $_SERVER['REQUEST_URI']), '/');
        $this->router->route($path);
    }

}