<?php
namespace AdmBereich;

class Session {

    static function start(){
        @session_start();
    }

    static function destroy(){
        Session::start();
        session_destroy();
    }

    /*private function isLogged(){
        return ($this->user !== false);
    }*/


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
        return $_SESSION[$index];
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