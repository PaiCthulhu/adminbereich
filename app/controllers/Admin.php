<?php
namespace abApp;
class Admin extends \AdmBereich\Controller {

    const MENU = [
            ['title'=>'Início', 'page'=>'admin.pages.dashboard','path'=>'admin','icon'=>'home','icon_type'=>'s'],
            ['title'=>'Banners', 'page'=>'admin.pages.banners','path'=>'admin','icon'=>'images','icon_type'=>'r'],
            ['title'=>'Publicações', 'page'=>'admin.pages.posts','path'=>'admin','icon'=>'newspaper','icon_type'=>'r'],
            ['title'=>'Categorias', 'page'=>'admin.pages.cats','path'=>'admin','icon'=>'tags','icon_type'=>'s'],
            ['title'=>'Emails', 'page'=>'admin.pages.mails','path'=>'admin','icon'=>'envelope','icon_type'=>'r'],
            ['title'=>'Opções', 'type'=>'cat'],
            ['title'=>'Usuários', 'page'=>'admin.pages.usuarios.*','path'=>'admin/usuarios','icon'=>'users','icon_type'=>'s'],
            ['title'=>'Configurações', 'page'=>'admin.pages.configs.read','path'=>'admin/configs','icon'=>'cogs','icon_type'=>'s']
          ];

    function login($params = array()){
        if(empty($params) || !is_array($params))
            parent::render('admin.login');
        else
            parent::render('admin.login', $params);
        if(\AdmBereich\Session::has('login_error'))
            \AdmBereich\Session::unset('login_error');
    }

    function logar(){
        if(!\AdmBereich\Auth::login($_POST['user'], $_POST['pswd'], 'Usuario')){
            \AdmBereich\Session::set('login_error', 'Login Inválido');
        }
        \AdmBereich\Router::redirect('admin/');
    }

    function sair(){
        \AdmBereich\Auth::logout();
        \AdmBereich\Router::redirect('admin/');
    }

    function index(){
        if (!\AdmBereich\Session::has('id'))
            $this->login();
        else
            parent::render('admin.pages.dashboard');
    }

}