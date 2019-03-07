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
 * Gerencia a superglobal de sessão
 *
 * Esta classe trabalha no sistema de instância única: Singleton
 * @package AdmBereich
 */
class Session {

    /**
     * Definição dos possíveis estados da sessão
     */
    const SESSION_STARTED = true,
          SESSION_NOT_STARTED = false;

    /**
     * @var bool $state Contém o estado atual da sessão
     */
    private $state = self::SESSION_NOT_STARTED;

    /**
     * @var Session $session Singleton
     */
    private static $session;

    /**
     * Session constructor.
     */
    private function __construct() {
        session_start();
        $this->state = self::SESSION_STARTED;
    }

    /**
     * Impede o acesso a duplicação da instância
     */
    private function __clone(){}
    /**
     * Impede o acesso a duplicação da instância
     */
    private function __wakeup(){}

    /**
     * Ponto de acesso à instância Singleton da classe Session
     * @return Session
     */
    static function start(){
        if(!isset(self::$session)){
            self::$session = new self();
        }

        return self::$session;
    }

    /**
     * Elimina todas as variáveis associadas à sessão
     */
    static function destroy(){
        Session::start();
        session_destroy();
    }

    /**
     * Define uma nova variável de sessão
     * @param string $index Nome da variável
     * @param mixed $val Valor
     */
    static function set($index, $val){
        Session::start();
        $_SESSION[$index] = $val;
    }

    /**
     * Elimina uma variável de sessão
     * @param string $index Nome da variável
     */
    static function unset($index){
        Session::start();
        unset($_SESSION[$index]);
    }

    /**
     * Obtém o valor de uma variável de sessão
     * @param string $index Nome da variável
     * @return mixed Valor
     */
    static function get($index){
        Session::start();
        return @$_SESSION[$index];
    }

    /**
     * Checa se a sessão possui uma variável
     * @param string $index Nome da variável
     * @return bool TRUE se ela existe e possui um valor, FALSE caso contrário
     */
    static function has($index){
        Session::start();
        return (!empty($_SESSION[$index]));
    }

    /**
     * Retorna a sessão e todas suas variáveis como um objeto
     * @return object
     */
    static function viewAll(){
        Session::start();
        return (object) $_SESSION;
    }
}