<?php

class Configs extends Controller{

    const DEFAULT_ROUTE = 'admin/';

    function __construct(){
        parent::__construct();
        $this->admin = true;
    }

    function index(){
        if (!Auth::hasPerm('configs_view'))
            Router::redirect(self::DEFAULT_ROUTE);
        $configs = new Config();
        $configs = $configs->all();
        parent::render('admin.pages.configs.read',['configs'=>$configs]);
    }

    function add(){
        if (!Auth::hasPerm('configs_add'))
            Router::redirect(self::DEFAULT_ROUTE);
        parent::render('admin.pages.configs.add');
    }

    function edit($id){
        if (!Auth::hasPerm('configs_edit'))
            Router::redirect(self::DEFAULT_ROUTE);
        $configs = new Config();
        $config = $configs->get($id);
        if($config === false)
            die($this->errorHandler("Configuração id: ({$id}) não encontrada!"));
        parent::render('admin.pages.configs.edit',['config'=>$config]);
    }

    function save(){
        if (!Auth::hasPerm('configs_add'))
            Router::redirect(self::DEFAULT_ROUTE);
        parent::save();
    }

    function update(){
        if (!Auth::hasPerm('configs_edit'))
            Router::redirect(self::DEFAULT_ROUTE);
        parent::update();
    }

    function delete($id){
        if (!Auth::hasPerm('configs_delete'))
            Router::redirect(self::DEFAULT_ROUTE);
        parent::delete($id);
    }

}