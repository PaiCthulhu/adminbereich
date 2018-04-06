<?php

class Configs extends CRUDController{

    const DEFAULT_ROUTE = 'admin/';

    function __construct(){
        parent::__construct();
        $this->_authPrefix = 'configs';
        $this->view_folder = 'admin';
    }

    function index(){
        $this->authCheck('view');
        $configs = new Config();
        $configs = $configs->all();
        parent::render('admin.pages.configs.read',['configs'=>$configs]);
    }

}