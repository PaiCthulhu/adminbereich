<?php
class DB {
    /**
     * @var PDO $handle Conexão com o banco de dados
     */
    protected $handle;

    /**
     * DB constructor.
     * @param string $host
     * @param string $user
     * @param string $pswd
     * @param string $db
     */
    function __construct($host, $user, $pswd, $db){
        $dsn = "mysql:host={$host};dbname={$db};charset=UTF8";
        try{
            $this->handle = new PDO($dsn, $user, $pswd);
        }
        catch (PDOException $e){
            $this->errorHandler("Falha ao conectar-se ao banco de dados", $e);
        }
    }

    /**
     * Fecha a conexão com o banco de dados
     */
    function __destruct(){
        $this->handle = null;
    }

    /**
     * Resolve uma query SQL
     * @param $query
     * @return PDOStatement|array Retorna o objeto PDOStatement resultado da query ou então um array com código e mensagem de erro
     */
    function query($query){
        $q = $this->handle->query($query);
        if($q === false)
            return $this->handle->errorInfo();
        $q->execute();
        return $q;
    }

    /**
     * @param $query
     * @param int $mode
     * @return array|bool
     */
    function fetch($query, $mode = PDO::FETCH_OBJ){
        $q = $this->query($query);
        if(is_array($q)){
            return $this->errorHandler("Erro ao executar SQL", $q, $query);
        }
        else if($q->rowCount() == 0)
            return false;
        else
            return $q->fetchAll($mode);
    }

    /**
     * @param string $search
     * @return array|bool
     */
    function selectTables($search = ''){
        $query = "SHOW TABLES";
        if(is_string($search) && $search != ''){
            $search = $this->sanitize($search);
            $query .= " LIKE '%{$search}%'";
        }
        $res = $this->fetch($query);
        if($res === false)
            return $res;
        else{
            $retorno = [];
            foreach ($res as $row){
                $retorno[] = $row->{'Tables_in_'.DB_NAME};
            }
            return $retorno;
        }
    }

    function selectAll($table, $mode = PDO::FETCH_OBJ){
        $query = "SELECT * FROM `{$table}`";
        return $this->fetch($query, $mode);
    }

    function selectSingle($table, $id, $mode = PDO::FETCH_OBJ){
        $k = $this->handle->query("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
        if($k === false){
            return $this->errorHandler("Tabela \"{$table}\" não encontrada!", $this->handle->errorInfo());
        }
        $k = $k->fetchAll(PDO::FETCH_OBJ)[0];
        $query = "SELECT * FROM `{$table}` WHERE `{$table}`.`{$k->Column_name}` = :id LIMIT 1";
        $q = $this->handle->prepare($query);
        $q->bindParam(':id', $id, PDO::PARAM_INT);
        $q->execute();
        return $q->fetchAll($mode)[0];
    }


    /**
     * @param $table
     * @param $params
     * @param int $mode
     * @return stdClass|bool
     */
    function selectSingleByFields($table, $params, $mode = PDO::FETCH_OBJ){
        $whereand = 'WHERE';
        $params = $this->sanitize($params);
        list($key, $val) = DB::keyValSplit($params);
        $query = "SELECT * FROM `{$table}`";
        foreach ($key as $i=>$v){
            $col = trim($key[$i], "'");
            $query .= " {$whereand} `{$table}`.`{$col}` = {$val[$i]}";
            if($whereand == 'WHERE')
                $whereand = 'AND';
        }
        $query .= " LIMIT 1";
        $q = $this->fetch($query, $mode);
        if(is_array($q))
            return $q[0];
        else
            return $q;
    }

    /**
     * Escapa variáveis para utilização em queries, no caso de arrays, faz o loop recursivo para escapar seus valores
     * @param array|string $data
     * @return array|string Variável com valores escapados
     */
    function sanitize($data){
        if(is_string($data))
            $retorno = $this->handle->quote($data);
        else if(is_array($data)){
            $retorno = [];
            foreach ($data as $key => $val)
                $retorno[$this->sanitize($key)] = $this->sanitize($val);
        }
        else
            $retorno = $data;
        return $retorno;
    }

    /**
     * Função para padronizar erros de banco de dados
     * @param string $msg
     * @param array|PDOException $errorInfo
     * @param string $query
     * @return bool
     */
    function errorHandler($msg, $errorInfo, $query = ''){
        if(is_array($errorInfo)){
            echo "{$msg}: ({$errorInfo[0]}) {$errorInfo[2]}";
            if($query != '')
                echo " - Query: \"{$query}\"";

        }
        else if(is_a($errorInfo, 'PDOException')){
            echo "{$msg}: ({$errorInfo->getCode()}) {$errorInfo->getMessage()}";
        }
        return false;
    }

    /**
     * @param $array
     * @return array
     */
    static function keyValSplit($array){
        return [array_keys($array), array_values($array)];
    }
}