<?php
class Site extends Controller {

    function home(){
        $u = new Usuario();
        parent::render('home', array('usuario'=>$u->getSingle(1)));
    }

}