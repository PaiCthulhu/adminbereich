<?php
class Admin extends Controller {

    function login($params = array()){
        if(empty($params) || !is_array($params))
            parent::render('admin.login');
        else
            parent::render('admin.login', $params);
        if(Session::has('login_error'))
            Session::unset('login_error');
    }

    function logar(){
        if(Usuarios::login($_POST['user'], $_POST['pswd'])){
            $this->index();
        }
        else{
            Session::set('login_error', 'Login Inválido');
        }
        Router::redirect('admin/');
    }

    function sair(){
        Session::destroy();
        Router::redirect('admin/');
    }

    function index(){
        if (!Session::has('id'))
            $this->login();
        else
            parent::render('admin.dashboard');
    }

}