<?php
class Site extends Controller {

    function home(){
        $u = new Usuario();
        parent::run(array('usuario'=>$u->getSingle(1)));
    }

}