<?php
class Model{
    protected $db, $_table, $_pk;

    function __construct(){
        $this->db = new DB(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
        $this->_table = strtolower(get_class($this));
        $this->_pk = 'id_'.$this->_table;
    }

    function all(){
        return $this->db->selectAll($this->_table);
    }

    function get($id){
        return $this->db->selectSingle($this->_table, $id);
    }


    function find($params){
        return $this->db->selectSingleByFields($this->_table, $params);
    }

    function create($params){
        return $this->db->insert($this->_table, $params);
    }

    function update($id, $params){
        return $this->db->update($this->_table, $params, $id);
    }

    function delete($id){
        return $this->db->delete($this->_table, [$this->_pk=>$id]);
    }

    function load($id){
        $l = $this->get($id);
        if($l === false)
            return false;
        return $this->cast($l, $this);
    }


    function pk(){
        return $this->_pk;
    }

    /**
     * @param stdClass $source
     * @param string|object $dest
     * @return object
     */
    function cast($source, $dest){
        if(is_string($dest))
            $dest = new $dest();
        foreach (get_object_vars($source) as $prop=>$val){
            $dest->$prop = $val;
        }
        return $dest;
    }

    /**
     * @param float $number
     * @return string
     */
    static function numberFormat($number, $decimals = 0){
        return number_format($number, $decimals, ',', '.');
    }
}