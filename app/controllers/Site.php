<?php
class Site extends Controller {

    const DBLESS = true;

    function home(){
        $config = new Config();
        parent::render('home', ['config'=>$config]);
    }

}