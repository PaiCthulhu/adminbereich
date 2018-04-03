<?php
class Model{
    public $created, $updated;
    protected $db, $_table, $_pk;

    function __construct(){
        $this->db = new DB(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
        $this->_table = strtolower(get_class($this));
        $this->_pk = 'id_'.$this->_table;
        $this->created = time();
    }

    function all(){
        return $this->db->selectAll($this->_table);
    }

    function get($id){
        return $this->db->selectSingle($this->_table, $id);
    }

    function getByField($field, $value){
        return $this->db->selectSingleByFields($this->_table, [$field=>$value]);
    }

    function getAllOrderBy($field = 'order', $desc = false){
        return $this->db->fetch("SELECT * FROM {$this->_table} ORDER BY `{$field}` ".(($desc)?'DESC':''));
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

    /**
     * @param int $id
     * @return bool|Model
     */
    static function load($id){
        $n = new static();
        $l = $n->get($id);
        if($l === false)
            return false;
        return $n->cast($l, $n);
    }


    function pk(){
        return $this->_pk;
    }

    /**
     * @param string|Model $relClass
     * @param string $relTable
     * @param string $fk1
     * @param string $fk2
     */
    function relMulti($relClass, $relTable = '', $fk = '', $pk = ''){
        if(is_string($relClass))
            $relClass = new $relClass();
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        $q = "SELECT {$fk} FROM {$relTable} WHERE {$pk} = ".$this->{$this->_pk};
        $r = $this->db->fetch($q)[0];
        return $relClass::load($r->{$relClass->_pk});
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