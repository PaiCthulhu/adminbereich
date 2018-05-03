<?php

namespace AdmBereich;

class Auth {

    /**
     * @param string $user
     * @param string $pswd
     * @param string $class
     * @return bool
     */
    static function login($user, $pswd, $class = USER_CLASS){
        $class = '\\'.DEFAULT_NAMESPACE.'\\'.$class;
        $usuario = new $class();
        $u = $usuario->find(['username'=>$user]);
        if($u === false)
            return false;
        $usuario = $usuario->load($u->id_usuario);
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