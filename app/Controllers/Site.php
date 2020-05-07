<?php
namespace abApp\Controllers;

use abApp\Models\Config;

class Site extends \AdmBereich\Controller {

    /**
     * @throws \Exception
     */
    function index(){
        $config = new Config();
        parent::render('home', ['config'=>$config]);
    }

    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    function _error($exception){
        $config = new Config();
        parent::render('pages.404', ['config'=>$config, 'erro'=>$exception->getMessage(),'dump'=>$exception]);
    }

}