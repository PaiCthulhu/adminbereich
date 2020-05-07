<?php
namespace abApp\Controllers\Admin;

use AdmBereich\CRUDController;

class Configs extends CRUDController {

    const DEFAULT_ROUTE = 'admin/';

    function __construct(){
        parent::__construct();
        $this->_authPrefix = 'configs';
        $this->view_folder = 'admin';
    }

}