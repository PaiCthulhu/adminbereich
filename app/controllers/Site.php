<?php
class Site extends Controller {

    const DBLESS = true;

    function home(){
        $config = new Config();
        parent::render('home', ['config'=>$config]);
    }

    function _error($exception){
        $config = new Config();
        parent::render('pages.404', ['config'=>$config, 'erro'=>$exception->getMessage(),'dump'=>$exception]);
    }

}