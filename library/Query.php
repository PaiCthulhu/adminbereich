<?php

namespace AdmBereich;

class Query{

    /**
     * @var string $query
     */
    protected $query;

    /**
     * @return string
     */
    function __toString(){
        return $this->getSQL();
    }

    /**
     * @return string
     */
    function getSQL(){
        return $this->query;
    }

    /**
     * @param string $table
     * @param array $fields
     * @return Query $this
     */
    function insert($table, $fields = []){
        $q = "INSERT INTO `{$table}`";
        if(!empty($fields)){
            $q .= "(";
            foreach ($fields as $k=>$field){
                $q .= "`{$field}`";
                $q.= ($k !== $this->arrayLastKey($fields))?", ":"";
            }
            $q .= ")";
        }
        $this->query = $q." ";
        return $this;
    }

    /**
     * @param array $params
     * @return Query $this
     */
    function values($params){
        $q = "VALUES ";
        if(is_array($params[0]))
            foreach ($params as $row){
                $q .= "(";
                foreach ($row as $k=>$val){
                    $q .= $this->valueEscape($val);
                    $q.= ($k !== $this->arrayLastKey($row))?", ":"";
                }
                $q .= ")";
            }
        else{
            $q .= "(";
            foreach ($params as $k=>$val){
                $q .= $this->valueEscape($val);
                $q.= ($k !== $this->arrayLastKey($params))?", ":"";
            }
            $q .= ")";
        }

        $this->query .= $q;
        return $this;
    }

    /**
     * @param string $table
     * @return Query $this
     */
    function update($table){
        $this->query = "UPDATE `{$table}` ";
        return $this;
    }

    /**
     * @param array $params
     * @return Query $this
     */
    function set($params){
        $q = 'SET ';
        $params = $this->arrayClearEmpty($params);
        foreach ($params as $key=>$value){
            $q.= "`{$key}` = ".$this->valueEscape($value);
            $q.= ($key !== $this->arrayLastKey($params))?", ":"";
        }
        $this->query .= $q." ";
        return $this;
    }

    /**
     * @param string $table
     * @return Query $this
     */
    function delete($table){
        $this->query = "DELETE FROM `{$table}` ";
        return $this;
    }

    /**
     * @param array $fields
     * @return Query $this
     */
    function select($fields = []){
        $q = "SELECT ";
        if(empty($fields))
            $q .= "* ";
        else
            foreach ($fields as $k=>$field){
                if($this->isAssoc($fields))
                    $q.= "`{$k}` AS ".$this->valueEscape($field);
                else if(is_array($field))
                    $q.= "`{$field[0]}` AS ".$this->valueEscape($field[1]);
                else
                    $q.= "`{$field}`";
                $q.= ($k !== $this->arrayLastKey($fields))?", ":" ";
            }

        $this->query = $q;
        return $this;
    }

    /**
     * @param string|array $table
     * @return Query $this
     */
    function from($table){
        if(is_array($table))
            $this->query .= "FROM `{$table[0]}`.`{$table[1]}` ";
        else
            $this->query .= "FROM `{$table}` ";
        return $this;
    }

    /**
     * @param array $conds
     * @param string $mode
     * @return Query $this
     */
    function where($conds = [], $mode = 'AND'){
        $q = "WHERE ";
        if(!empty($conds)){
            if(!$this->isAssoc($conds) AND count($conds) == 2)
                $q .= "`{$conds[0]}` = ".$this->valueEscape($conds[1]);
            else
                foreach ($conds as $key=>$row){
                    if(!is_array($row))
                        if(is_null($row))
                            $q .= "`{$key}` IS ".$this->valueEscape($row);
                        else
                            $q .= "`{$key}` = ".$this->valueEscape($row);
                    else
                        if(count($row) == 2)
                            $q .= "`{$key}` {$row[0]} ".$this->valueEscape($row[1]);
                        else if(count($row) == 3)
                            $q .= "`$row[0]` {$row[1]} ".$this->valueEscape($row[2]);
                    $q.= ($key !== $this->arrayLastKey($conds))?" {$mode} ":" ";
                }
        }
        else
            $q .= "1";
        $this->query .= $q." ";
        return $this;
    }

    /**
     * @param string|array $field
     * @param string $ord
     * @return Query $this
     */
    function orderBy($field, $ord = 'ASC'){
        $q = "ORDER BY ";
        if(is_array($field)){
            foreach ($field as $k=>$row){
                if(is_array($row))
                    list($f,$m) = [$row[0],$row[1]];
                else
                    list($f,$m) = [$row,$ord];
                if(substr($f, 0, 1) === '-'){
                    $f = ltrim($f, '-');
                    $q .= '-';
                }
                $q .= "`{$f}` {$m}";
                $q.= ($k !== $this->arrayLastKey($field))?", ":" ";
            }
        }
        else
            $q .= "`{$field}` {$ord}";

        $this->query .= $q;
        return $this;
    }

    /**
     * @param int|array $limit
     * @param int $offset
     * @return Query $this
     */
    function limit($limit, $offset = 0){
        if(is_array($limit))
            $q = "LIMIT {$limit[0]}, {$limit[1]}";
        else{
            $q = "LIMIT {$limit}";
            if(!empty($offset))
                $q .= " OFFSET {$offset}";
        }
        $this->query .= $q;
        return $this;
    }

    /**
     * @return Query $this
     */
    function showTables(){
        $this->query = "SHOW TABLES ";
        return $this;
    }

    /**
     * @param string $index
     * @return $this
     *
     */
    function showIndex($index = 'INDEX'){
        $this->query = "SHOW {$index} ";
        return $this;
    }

    /**
     * Aquela exceção.... adiciona manualmente strings à Query
     * @param string $string
     * @return Query $this
     */
    function extra($string){
        $this->query .= $string;
        return $this;
    }

    /**
     * @param mixed $val
     * @return bool|string
     */
    protected function valueEscape($val){
        if(is_null($val))
            return 'NULL';
        else if(is_bool($val))
            return ($val)?'TRUE':'FALSE';
        else if(is_string($val)){
            if(preg_match('/^\:[^\s\:]*(?<!:)/', $val) == 1 && $val != ':') //Check if :param
                return $val;
            else
                return "'{$val}'";
        }
        else if(is_int($val))
            return $val;
        else
            dump($val); //Todo implement more options
        return false;
    }

    /**
     * Checa se a array é uma matriz associativa ou não
     * @param array $array
     * @return bool
     */
    protected function isAssoc(array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * @param array $array
     * @return array
     */
    protected function arrayClearEmpty($array){
        return array_filter($array, function ($value) {
            return isset($value) && $value !== null;
        });
    }

    protected function arrayLastKey($array){
        end($array);
        return key($array);
    }
}