<?php
namespace abApp\Controllers;

class Configs extends \AdmBereich\CRUDController{

    const DEFAULT_ROUTE = 'admin/';

    function __construct(){
        parent::__construct();
        $this->_authPrefix = 'configs';
        $this->view_folder = 'admin';
    }

}