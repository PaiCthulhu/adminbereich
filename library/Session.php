<?php
namespace AdmBereich;

class Session {

    const SESSION_STARTED = true,
          SESSION_NOT_STARTED = false;

    private $state = self::SESSION_NOT_STARTED;

    /**
     * @var Session $session Singleton
     */
    private static $session;

    private function __construct() {
        session_start();
        $this->state = self::SESSION_STARTED;
    }

    private function __destruct()
    {
        self::destroy();
    }

    private function __clone(){ }
    private function __wakeup(){ }

    static function start(){
        if(!isset(self::$session)){
            self::$session = new self();
        }

        return self::$session;
    }

    static function destroy(){
        Session::start();
        session_destroy();
    }

    static function set($index, $val){
        Session::start();
        $_SESSION[$index] = $val;
    }

    static function unset($index){
        Session::start();
        unset($_SESSION[$index]);
    }

    static function get($index){
        Session::start();
        return @$_SESSION[$index];
    }

    static function has($index){
        Session::start();
        return (!empty($_SESSION[$index]));
    }

    static function viewAll(){
        Session::start();
        return (object) $_SESSION;
    }
}