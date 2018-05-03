<?php
namespace abApp;
class Site extends \AdmBereich\Controller {

    function home(){
        $config = new Config();
        parent::render('home', ['config'=>$config]);
    }

    function _error($exception){
        $config = new Config();
        parent::render('pages.404', ['config'=>$config, 'erro'=>$exception->getMessage(),'dump'=>$exception]);
    }

}