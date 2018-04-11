<?php
class Model{
    public $created, $updated;
    protected $db, $_table, $_pk, $_columns;

    function __construct(){
        $this->db = new DB(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
        $this->_table = strtolower(get_class($this));
        $this->_pk = sprintf(DB_PK_FORMAT, $this->_table);
        $this->created = date('Y-m-d G:i:s');
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

    function findAll($params){
        return $this->db->selectAllByFields($this->_table, $params);
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

    function save(){
        $this->_loadColumns();
        $opt = [];
        foreach ($this->_columns as $column)
            $opt[$column->name] = $this->columnCheck($column, $this->{$column->name});
        if(isset($opt[$this->_pk]) && !empty($opt[$this->_pk])){
            $id = $opt[$this->_pk];
            unset($opt[$this->_pk]);
            $r = $this->update($id, $opt);
        }
        else {
            unset($opt[$this->_pk]);
            $r = $this->create($opt);
        }

        return $r;
    }

    /**
     * @param int $id
     * @return bool|static
     */
    static function load($id){
        $n = new static();
        $l = $n->get($id);
        if($l === false)
            return false;
        return $n->cast($l, $n);
    }

    function getTable(){
        return $this->_table;
    }

    function pk(){
        return $this->_pk;
    }

    function lastId(){
        return $this->db->lastId();
    }

    /**
     * @throws Exception
     */
    function _loadColumns(){
        $c = $this->db->selectColumns($this->_table);
        if($c === false)
            throw new Exception('Erro ao carregar colunas do banco de dados');
        $this->_columns = $c;
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
     * @param Model $relClass
     * @param string $relTable
     * @param string $fk
     * @param string $pk
     * @return array|bool
     */
    function relAttach($relClass, $relTable = '', $fk = '', $pk = ''){
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        $q = "INSERT INTO {$relTable}(`{$pk}`,`{$fk}`) VALUES ({$this->{$pk}}, {$relClass->{$fk}})";
        return $this->db->run($q);

    }

    function relDetach($relClass, $relTable = '', $fk = '', $pk = ''){
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        $q = "DELETE FROM {$relTable} WHERE `{$pk}` = {$this->{$pk}} AND `{$fk}` = {$relClass->{$fk}}";
        return $this->db->run($q);
    }

    function relDetachAll($relClass, $relTable = '', $pk = ''){
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $q = "DELETE FROM {$relTable} WHERE `{$pk}` = {$this->{$pk}}";
        return $this->db->run($q);
    }

    /**
     * @param stdClass $source
     * @param string|Model $dest
     * @return Model
     */
    private function cast($source, $dest){
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

    private function columnCheck($column, $value = null){
        //Check Null
        if($value === null && $column->cannull == 'NO')
            if($column->name == $this->_pk && $column->index == 'PRI')
                return null;
            else if($column->default == 'CURRENT_TIMESTAMP')
                return $this->created;
            else
                throw new Exception("Coluna '{$column->name}' não pode ter um valor nulo");

        //Check int
        if($column->type == 'int' && !is_int($value))
            if(is_string($value) && ctype_digit($value))
                return $value+0;
            else
                throw new Exception("Coluna '{$column->name}' requer um valor inteiro, ".Dict::translate(gettype($value))." recebido...");

        //Check String
        if($column->type == 'varchar'){
            //tipo
            if(!is_string($value))
                if(is_object($value) && method_exists($value,'__toString'))
                    $value = $value->__toString();
                else
                    throw new Exception("Coluna '{$column->name}' requer um valor de texto, ".Dict::translate(gettype($value))." recebido...");
            //tamanho
            if(strlen($value) > $column->length)
                throw new Exception("O valor recebido (".strlen($value)." caracteres) ultrapassa o limite de tamanho da coluna '{$column->name}' que é de {$column->length}.");

        }

        return $value;
    }

}