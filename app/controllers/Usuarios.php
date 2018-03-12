<?php
class Usuarios extends Controller {

    function __construct(){
        parent::__construct();
        $this->admin = true;
    }

    function index(){
        if (!Auth::hasPerm('users_view'))
            Router::redirect('admin/');
        $usuarios = new Usuario();
        $usuarios = $usuarios->all();
        parent::render('admin.pages.usuarios.read',['usuarios'=>$usuarios]);
    }

    function add(){
        if (!Auth::hasPerm('users_add'))
            Router::redirect('admin/');
        parent::render('admin.pages.usuarios.add');
    }

    function edit($id){
        if (!Auth::hasPerm('users_edit'))
            Router::redirect('admin/');
        $usuarios = new Usuario();
        $usuario = $usuarios->get($id);
        if(!$usuario)
            throw new Exception("O usuário id #{$id} não existe ou não foi encontrado");
        else
            parent::render('admin.pages.usuarios.edit', ['usuario'=>$usuario]);
    }

    function save(){
        if (!Auth::hasPerm('users_add'))
            Router::redirect('admin/');
        $_POST['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        parent::save();
    }

    function update(){
        if (!Auth::hasPerm('users_edit'))
            Router::redirect('admin/');
        if(isset($_POST['senha']) && !empty($_POST['senha']))
            $_POST['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        else
            unset($_POST['senha']);
        parent::update();
    }

    function delete($id){
        if (!Auth::hasPerm('users_delete'))
            Router::redirect('admin/');
        parent::delete($id);
    }


}