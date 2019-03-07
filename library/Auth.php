<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich;

/**
 * Classe básica de autenticação, com as bases de login, logout e permissões
 * @package AdmBereich
 */
class Auth {

    /**
     * Checa a credenciais e então inicia a sessão e registra os dados do usuário logado
     * @param string $user Usuário
     * @param string $pswd Senha
     * @param string $class Classe de verificação
     * @return bool TRUE caso login tenha se sucedido, FALSE caso contrário
     */
    static function login($user, $pswd, $class = USER_CLASS){
        $class = '\\'.DEFAULT_NAMESPACE.'\\'.$class;
        $usuario = new $class();
        $u = $usuario->find(['username'=>$user]);
        if($u === false)
            return false;
        $usuario = $usuario->load($u->usuario_id);
        if(password_verify($pswd, $usuario->senha)){
            Session::set('mail', $usuario->email);
            Session::set('pswd', $pswd);
            Session::set('nome', $usuario->nome);
            Session::set('user', $usuario->username);
            Session::set('id', $usuario->usuario_id);
            return true;
        }
        else{
            Session::destroy();
            return false;
        }

    }

    /**
     * Destrói as variáveis de sessão
     */
    static function logout(){
        Session::destroy();
    }

    /**
     * Função base de checagem de permissões
     * @param int|string $permission Id ou Tag da Permissão
     * @return bool TRUE se o usuário possuir a permissão, FALSE caso contrário
     */
    static function hasPerm($permission){
        return Session::has('id');
    }

}