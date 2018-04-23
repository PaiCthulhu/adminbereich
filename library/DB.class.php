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
        $q = new Query();
        $q->showTables();
        if(is_string($search) && $search != ''){
            $search = $this->sanitize($search);
            $q->extra(" LIKE '%{$search}%'");
        }
        $res = $this->fetch($q);
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

    function selectColumns($table){
        $q = new Query();
        $q->select(
            ['ORDINAL_POSITION'=>'col_id',
             'COLUMN_NAME'=>'name',
             'COLUMN_DEFAULT'=>'default',
             'IS_NULLABLE'=>'cannull',
             'DATA_TYPE'=>'type',
             'CHARACTER_MAXIMUM_LENGTH'=>'length',
             'COLUMN_KEY'=>'index'])->from(['INFORMATION_SCHEMA','COLUMNS'])->where(['TABLE_SCHEMA'=>DB_NAME,'TABLE_NAME'=>$table]);
        return $this->fetch($q);
    }

    function selectAll($table, $mode = PDO::FETCH_OBJ){
        $q = new Query();
        return $this->fetch($q->select()->from($table), $mode);
    }

    /**
     * @param $table
     * @param $id
     * @param int $mode
     * @return bool|array|stdClass
     */
    function selectSingle($table, $id, $mode = PDO::FETCH_OBJ){
        $q = new Query();
        $k = $this->handle->query($q->showIndex('KEYS')->from($table)->where(['Key_name','PRIMARY']));
        if($k === false){
            return $this->errorHandler("Tabela \"{$table}\" não encontrada!", $this->handle->errorInfo());
        }
        $k = $k->fetchAll(PDO::FETCH_OBJ)[0];
        $q->select()->from($table)->where(["{$table}`.`{$k->Column_name}", ":id"])->limit(1);
        $q = $this->handle->prepare($q);
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
        $q = new Query();
        $r = $this->fetch($q->select()->from($table)->where($params)->limit(1), $mode);
        if(is_array($r))
            return $r[0];
        else
            return $r;
    }

    /**
     * @param $table
     * @param $params
     * @param int $mode
     * @return stdClass|bool
     */
    function selectAllByFields($table, $params, $mode = PDO::FETCH_OBJ){
        return $this->fetch((new Query())->select()->from($table)->where($params), $mode);
    }

    /**
     * Cria e executa uma query de inserção no banco de dados, com os valores de um array
     * @param string $table Nome da tabela
     * @param array $params Array de dados a serem inseridos, onde a chave deve ser o nome do campo
     * @return array|bool Retorna TRUE caso a inserção suceda, caso contrário, retorna o array com código e mensagem do erro
     */
    function insert($table, $params){
        $q = new Query();
        list($fields, $values) = DB::keyValSplit($params);
        return $this->run($q->insert($table, $fields)->values($values));
    }

    /**
     * @param string $table Nome da tabela
     * @param array $params Array de dados a serem atualizados, onde a chave deve ser o nome do campo
     * @param array|int $id todo escrever a explicação disso aqui
     * @return array|bool Retorna TRUE caso a inserção suceda, caso contrário, retorna o array com código e mensagem do erro
     */
    function update($table, $params, $id){
        $q = new Query();
        if(!is_array($id))
            $id = [sprintf(DB_PK_FORMAT, $table), $id];
        return $this->query($q->update($table)->set($params)->where($id));
    }

    /**
     * @param string $table Nome da tabela
     * @param array $params Array de parâmetros para a exclusão, baseado na Query::where
     * @return array|PDOStatement
     */
    function delete($table, $params){
        $q = new Query();
        if(!is_array($params) || empty($params))
            return [-1,-1,"Parâmetros Inválidos"];
        return $this->query($q->delete($table)->where($params));
    }

    /**
     * @param $query
     * @return array|bool
     */
    function run($query){
        $q = $this->handle->prepare($query);
        if($q === false)
            return $this->handle->errorInfo();
        $r = $q->execute();
        if($r === false)
            return [-1, -1, 'Erro ao executar query: '.$query];
        return $r;
    }

    function lastId(){
        return $this->handle->lastInsertId();
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