<?php
class Site extends Controller {

    const DBLESS = true;

    function home(){
        $u = new Usuario();
        parent::render('home', array('usuario'=>$u->get(1)));
    }

}