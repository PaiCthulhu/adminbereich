<?php
class Usuarios {
    static function login($user, $pswd){
        $usuarios = new Usuario();
        $usuario = $usuarios->find(['username'=>$user]);
        if($usuario === false)
            return $usuario;
        if(password_verify($pswd, $usuario->senha)){
            Session::set('mail', $usuario->email);
            Session::set('pswd', $pswd);
            Session::set('nome', $usuario->nome);
            Session::set('user', $usuario->username);
            Session::set('id', $usuario->id_usuario);
            return true;
        }
        else{
            Session::destroy();
            return false;
        }

    }
}