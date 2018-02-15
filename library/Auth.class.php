<?php

class Auth {

    static function login($user, $pswd, $class = 'User'){
        $usuarios = new $class();
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

    static function logout(){
        Session::destroy();
    }

    /**
     * @param int|string $permission Id ou Tag da Permissão
     * @return bool
     */
    static function hasPerm($permission){
        return Session::has('id');
    }

}