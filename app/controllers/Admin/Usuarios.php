<?php
namespace abApp\Controllers\Admin;

use AdmBereich\CRUDController;

class Usuarios extends CRUDController {

    const DEFAULT_ROUTE = 'admin/';

    function __construct(){
        parent::__construct();
        $this->_authPrefix = 'users';
        $this->view_folder = 'admin';
    }

    function save(){
        $_POST['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        parent::save();
    }

    function update(){
        if(isset($_POST['senha']) && !empty($_POST['senha']))
            $_POST['senha'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        else
            unset($_POST['senha']);
        parent::update();
    }


}