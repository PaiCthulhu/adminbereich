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

    /**
     * @param $table
     * @param $id
     * @param int $mode
     * @return bool|array|stdClass
     */
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
        if($q->rowCount() == 0)
            return false;
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
     * Cria e executa uma query de inserção no banco de dados, com os valores de um array
     * @param string $table Nome da tabela
     * @param array $params Array de dados a serem inseridos, onde a chave deve ser o nome do campo
     * @return array|bool Retorna true caso a inserção suceda, caso contrário, retorna o array com código e mensagem do erro
     */
    function insert($table, $params){
        $fields = $values = '';
        foreach ($params as $key=>$value){
            $fields.= '`'.$key.'`,';
            if(is_array($value) || is_string($value))
                $value = json_encode($value);
            $values.= $value.",";
        }
        $fields = rtrim($fields,',');
        $values = rtrim($values,',');
        $query = "INSERT INTO `".$table."` (".$fields.") VALUES (".$values.")";

        return $this->run($query);
    }

    /**
     * @param string $table
     * @param array $params
     * @param mixed $id
     * @return array|bool
     */
    function update($table, $params, $id){
        $changes = '';
        foreach ($params as $key=>$value){
            if(!empty($value))
                $changes.= '`'.$key.'` = '.json_encode($value).',';
        }
        $changes = rtrim($changes,',');

        if(is_array($id)){
            $key = array_keys($id);
            $identity = "`{$key[0]}` = ".json_encode($id[$key[0]]);
        }
        else
            $identity = "`id_{$table}` = {$id}";

        $query = "UPDATE `".$table."` SET  ".$changes." WHERE ".$identity;
        return $this->query($query);
    }

    function delete($table, $params){
        $where = [];
        if(!is_array($params) || empty($params))
            return [-1,-1,"Parâmetros Inválidos"];
        foreach ($params as $key=>$value)
            $where[] = "`{$key}` = ".json_encode($value);
        $query = "DELETE FROM `".$table."` WHERE ".implode(' AND ', $where);
        return $this->query($query);
    }

    function run($query){
        $q = $this->handle->prepare($query);
        if($q === false)
            return $this->handle->errorInfo();
        return $q->execute();
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