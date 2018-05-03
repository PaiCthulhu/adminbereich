<?php
namespace abApp;
class Config extends \AdmBereich\Model {

    function __construct(){
        parent::__construct();
        $this->_pk = 'id';
    }

    function getByKey($key){
        return $this->getByField('key', $key)->val;
    }

    function update($id, $params){
        return $this->db->update($this->_table, $params, ['id'=>$id]);
    }

}