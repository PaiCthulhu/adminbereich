<?php
namespace AdmBereich;

class Model{
    public $created, $updated;
    protected $db, $_table, $_pk, $_columns;

    /**
     * Model constructor.
     * @throws \ReflectionException
     */
    function __construct(){
        $this->db = DB::connection();
        $this->_table = strtolower(static::name());
        $this->_pk = sprintf(DB_PK_FORMAT, $this->_table);
        if(!isset($this->created))
            $this->created = date('Y-m-d G:i:s');
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

    function all($fetch_class = true){
        if($fetch_class)
            return $this->db->selectAll($this->_table, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectAll($this->_table);
    }

    function get($id, $fetch_class = true){
        if($fetch_class)
            return $this->db->selectSingle($this->_table, $id, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectSingle($this->_table, $id);
    }

    function getByField($field, $value, $fetch_class = true){
        if($fetch_class)
            return $this->db->selectSingleByFields($this->_table, [$field=>$value], \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectSingleByFields($this->_table, [$field=>$value]);
    }

    function getAllOrderBy($field = 'order', $desc = false, $fetch_class = true){
        $q = "SELECT * FROM {$this->_table} ORDER BY `{$field}` ".(($desc)?'DESC':'');
        if($fetch_class)
            return $this->db->fetch($q, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->fetch($q);
    }

    /**
     * @param $params
     * @return bool|\stdClass False or stdClass
     */
    function find($params, $fetch_class = true){
        if($fetch_class)
            return $this->db->selectSingleByFields($this->_table, $params, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectSingleByFields($this->_table, $params);
    }

    /**
     * @param $params
     * @param bool $fetch_class
     * @return bool|\stdClass
     */
    function findAll($params, $fetch_class = true){
        if($fetch_class)
            return $this->db->selectAllByFields($this->_table, $params, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectAllByFields($this->_table, $params);
    }

    function findAllOrderBy($params, $orderField, $desc = false, $fetch_class = true){
        $q = new Query();
        $mode = ($desc)? 'DESC':'ASC';
        $q->select()->from($this->_table)->where($params)->orderBy($orderField, $mode);
        if($fetch_class)
            return $this->db->fetch($q, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->fetch($q);
    }

    /**
     * @param array $params Array de dados a serem inseridos, onde a chave deve ser o nome do campo
     * @return array|bool Retorna TRUE caso suceda, do contrário, um array com o erro
     */
    function create($params){
        return $this->db->insert($this->_table, $params);
    }

    /**
     * @param int $id
     * @param array $params Array de dados a serem inseridos, onde a chave deve ser o nome do campo
     * @return array|bool Retorna TRUE caso suceda, do contrário, um array com o erro
     */
    function update($id, $params){
        return $this->db->update($this->_table, $params, [$this->_pk=>$id]);
    }

    /**
     * @param $id
     * @return array|\PDOStatement
     */
    function delete($id){
        return $this->db->delete($this->_table, [$this->_pk=>$id]);
    }

    /**
     * @todo Explicar melhor isso aqui
     * @return array|bool
     * @throws \Exception
     */
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
     * @return static|bool
     * @throws \ReflectionException
     */
    static function load($id){
        $n = new static();
        return $n->db->selectSingle($n->_table, $id, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * @return array|bool
     * @throws \ReflectionException
     */
    static function loadAll(){
        $n = new static();
        return $n->db->selectAll($n->_table, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * @throws \Exception
     */
    function _loadColumns(){
        $c = $this->db->selectColumns($this->_table);
        if($c === false)
            throw new \Exception('Erro ao carregar colunas do banco de dados');
        $this->_columns = $c;
    }

    /**
     * @param string|Model $relClass Other-Table class
     * @param string $relTable Relational-table
     * @param string $fk Other-Table primary key
     * @param string $pk Own primary key
     * @return array|bool
     */
    function relGet($relClass, $relTable = '', $fk = '', $pk = ''){
        if(is_string($relClass))
            $relClass = new $relClass();
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        $q = new Query();
        $q->select()->from($relTable)->where([$pk=>$this->{$pk}, $fk=>$relClass->{$fk}]);
        return $this->db->fetch($q);
    }

    /**
     * @param string|Model $relClass
     * @param string $relTable
     * @param string $fk
     * @param string $pk
     * @return Model
     * @throws \ReflectionException
     */
    function relSingle($relClass, $relTable = '', $fk = '', $pk = ''){
        if(is_string($relClass))
            $relClass = new $relClass();
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        $q = "SELECT {$fk} FROM {$relTable} WHERE {$pk} = ".$this->{$this->_pk};
        $r = $this->db->fetch($q)[0];
        return $relClass::load($r->{$relClass->_pk});
    }

    function relMultiList($relClass, $relTable = '', $fk = '', $pk = ''){
        $return = [];
        if(is_string($relClass))
            $relClass = new $relClass();
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        $q = new Query();
        $list = $this->db->fetch($q->select([$fk])->from($relTable)->where([$pk=>($this->{$this->_pk})]));
        if($list !== false)
            foreach ($list as $item)
                $return[] = $item->{$fk};
        return $return;
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
     * @param string $q
     * @return array|bool
     */
    function run($q){
        return $this->db->fetch($q);
    }

    /**
     * @param \stdClass $source
     * @param string|Model $dest
     * @return Model
     */
    protected function cast($source, $dest){
        if(is_string($dest))
            $dest = new $dest();
        foreach (get_object_vars($source) as $prop=>$val){
            $dest->$prop = $val;
        }
        return $dest;
    }


    /**
     * @param \stdClass $column
     * @param mixed $value
     * @return mixed
     * @throws \Exception
     */
    protected function columnCheck($column, $value = null){
        //Check Null
        if($value === null && $column->cannull == 'NO')
            if($column->name == $this->_pk && $column->index == 'PRI')
                return null;
            else if($column->default == 'CURRENT_TIMESTAMP')
                return $this->created;
            else
                throw new \Exception("Coluna '{$column->name}' não pode ter um valor nulo");

        //Check int
        if($column->type == 'int' && !is_int($value))
            if(is_string($value) && ctype_digit($value))
                return (int) $value+0;
            else if($column->cannull == 'YES' AND $value === null)
                return null;
            else
                throw new \Exception("Coluna '{$column->name}' requer um valor inteiro, ".Dict::translate(gettype($value))." recebido...");

        //Check String
        if($column->type == 'varchar'){
            //tipo
            if(!is_string($value))
                if(is_object($value) && method_exists($value,'__toString'))
                    $value = $value->__toString();
                else
                    throw new \Exception("Coluna '{$column->name}' requer um valor de texto, ".Dict::translate(gettype($value))." recebido...");
            //tamanho
            if(strlen($value) > $column->length)
                throw new \Exception("O valor recebido (".strlen($value)." caracteres) ultrapassa o limite de tamanho da coluna '{$column->name}' que é de {$column->length}.");

        }

        return $value;
    }

    /**
     * @param float $number
     * @param int $decimals Number of decimal digits
     * @return string
     */
    static function numberFormat($number, $decimals = 0){
        return number_format($number, $decimals, ',', '.');
    }

    static function dateTimeFormat($timestamp, $order = 'date'){
        if(is_string($timestamp))
            $timestamp = strtotime($timestamp);
        if($order == 'time')
            return date('H:i:s d/m/Y', $timestamp);
        else
            return date('d/m/Y H:i:s', $timestamp);
    }

    static function dateFormat($timestamp){
        if(is_string($timestamp))
            $timestamp = strtotime($timestamp);
        return date('d/m/Y', $timestamp);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    static function name(){
        return (new \ReflectionClass(get_called_class()))->getShortName();
    }

}