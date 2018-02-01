<?php
class Model{
    protected $db, $_table;

    function __construct(){
        $this->db = new DB(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
        $this->_table = strtolower(get_class($this));
    }

    function getSingle($id){
        return $this->db->selectSingle($this->_table, $id)[0];
    }
}