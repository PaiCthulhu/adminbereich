<?php
class Usuarios extends Controller {

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


}