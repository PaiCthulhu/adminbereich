<?php

class Configs extends Controller{

    function index(){
        if (!Auth::hasPerm('configs_view'))
            Router::redirect('admin/');
        $configs = new Config();
        $configs = $configs->all();
        parent::render('admin.pages.configs.read',['configs'=>$configs]);
    }

}