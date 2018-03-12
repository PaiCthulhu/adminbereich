<?php

class Config extends Model {

    function __construct(){
        parent::__construct();
        $this->_pk = 'id';
    }

    function update($id, $params){
        return $this->db->update($this->_table, $params, ['id'=>$id]);
    }

}