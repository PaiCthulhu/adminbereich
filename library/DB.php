<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich;

/**
 * Classe de acesso ao banco de dados
 *
 * Essa classe serve de embrulho para as operações da classe \PDO
 * Ela trabalha no sistema de instância única: Singleton
 * @package AdmBereich
 */
class DB {
    /**
     * @var DB $db Singleton
     */
    public static $db;
    /**
     * @var \PDO $handle Conexão com o banco de dados
     */
    protected $handle;

    /**
     * Construtor da classe DB
     * @param string $host Endereço do serviço
     * @param string $user Usuário de login no banco de dados
     * @param string $pswd Senha do login do banco de dados
     * @param string $db Base de dados do banco a ser acessada
     */
    private function __construct($host, $user, $pswd, $db, $port = 3306){
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=UTF8";
        try{
            $this->handle = new \PDO($dsn, $user, $pswd);
        }
        catch (\PDOException $e){
            $this->errorHandler("Falha ao conectar-se ao banco de dados", $e);
            die();
        }
    }

    /**
     * Fecha a conexão com o banco de dados
     */
    function __destruct(){
        $this->handle = null;
    }

    /**
     * Impede o acesso a duplicação da instância
     */
    private function __clone(){ }

    /**
     * Impede o acesso a duplicação da instância
     */
    private function __wakeup(){ }

    /**
     * Ponto de acesso à instância Singleton da classe DB
     * @return DB
     */
    public static function connection(){
        if(!isset(self::$db))
            self::$db = new self($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PSWD'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);

        return self::$db;
    }



    /**
     * Resolve uma query SQL
     * @param string $query
     * @return \PDOStatement|array Retorna o objeto PDOStatement resultado da query ou então um array com código e mensagem de erro
     */
    function query($query){
        $q = $this->handle->query($query);
        if($q === false)
            return $this->handle->errorInfo();
        $q->execute();
        return $q;
    }

    /**
     * Faz uma busca de registros a partir de uma query SQL
     * @param string $query Uma query do SQL
     * @param int $mode Modo de fetch do PDO, principais valores são: PDO::FETCH_OBJ, PDO::FETCH_CLASS e PDO::FETCH_ARRAY
     * @param mixed $arg Argumento para a função PDO::fetchAll()
     * @return array|false
     */
    function fetch($query, $mode = \PDO::FETCH_OBJ, $arg = null){
        $q = $this->query($query);
        if(is_array($q)){
            return $this->errorHandler("Erro ao executar SQL", $q, $query);
        }
        else if($q->rowCount() == 0)
            return false;
        else
            if(!empty($arg))
                 return $q->fetchAll($mode, $arg);
            else
                return $q->fetchAll($mode);
    }

    /**
     * Busca e exibe a lista das tabelas
     * @param string $search Se passado um valor, filtra as tabelas listadas
     * @return array|bool FALSE se nada for encontrado, se não retorna a listagem das tabelas
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
                $retorno[] = $row->{"Tables_in_{$_ENV['DB_NAME']}"};
            }
            return $retorno;
        }
    }

    /**
     * Lista as colunas da tabela $table
     * @param string $table Nome da tabela
     * @return array|bool FALSE se ocorrer um erro, senão trás um array listando as colunas
     */
    function selectColumns($table){
        $q = new Query();
        $q->select(
            ['ORDINAL_POSITION'=>'col_id',
             'COLUMN_NAME'=>'name',
             'COLUMN_DEFAULT'=>'default',
             'IS_NULLABLE'=>'cannull',
             'DATA_TYPE'=>'type',
             'CHARACTER_MAXIMUM_LENGTH'=>'length',
             'COLUMN_KEY'=>'index'])->from(['INFORMATION_SCHEMA','COLUMNS'])->where(['TABLE_SCHEMA'=>$_ENV['DB_NAME'],'TABLE_NAME'=>$table]);
        return $this->fetch($q);
    }

    /**
     * Seleciona todos os registros da tabela $table
     * @param string $table Nome da tabela
     * @param int $mode Modo de fetch do PDO, principais valores são: PDO::FETCH_OBJ, PDO::FETCH_CLASS e PDO::FETCH_ARRAY
     * @param null $classname Nome da classe pra ser instânciada caso seja passado o parâmetro PDO::FETCH_CLASS
     * @return array|bool FALSE se nenhum registro for encontrado, se não retorna o array da listagem
     */
    function selectAll($table, $mode = \PDO::FETCH_OBJ, $classname = null){
        $q = new Query();
        return $this->fetch($q->select()->from($table), $mode, $classname);
    }

    /**
     * Seleciona um registro através do id na tabela $table
     * @param string $table Nome da tabela
     * @param int $id Id
     * @param int $mode Modo de fetch do PDO
     * @param string|null $classname Nome da classe a ser instanciada, caso $mode seja PDO::FETCH_CLASS
     * @return bool|array|\stdClass
     */
    function selectSingle($table, $id, $mode = \PDO::FETCH_OBJ, $classname = null){
        $q = new Query();
        $k = $this->handle->query($q->showIndex('KEYS')->from($table)->where(['Key_name','PRIMARY']));
        if($k === false){
            return $this->errorHandler("Tabela \"{$table}\" não encontrada!", $this->handle->errorInfo());
        }
        $k = $k->fetchAll(\PDO::FETCH_OBJ)[0];
        $q->select()->from($table)->where(["{$table}`.`{$k->Column_name}", ":id"])->limit(1);
        $q = $this->handle->prepare($q);
        $q->bindParam(':id', $id, \PDO::PARAM_INT);
        $q->execute();
        if($q->rowCount() == 0)
            return false;
        return $q->fetchAll($mode, $classname)[0];
    }


    /**
     * Seleciona um único registro da tabela $table filtrando pelo parâmetros de $params
     * @param string $table Nome da tabela a ser acessada
     * @param array $params Lista dos parâmetros, consulte Query.where()
     * @param int $mode Código do método de retorno, seguinto as constantes da classe PDO
     * @param string|null $classname Nome da classe a ser instanciada, caso $mode seja PDO::FETCH_CLASS
     * @return \stdClass|false
     */
    function selectSingleByFields($table, $params, $mode = \PDO::FETCH_OBJ, $classname = null){
        $q = new Query();
        $r = $this->fetch($q->select()->from($table)->where($params)->limit(1), $mode, $classname);
        if(is_array($r))
            return $r[0];
        else
            return $r;
    }

    /**
     * Seleciona tudo da tabela $table filtrando pelo parâmetros de $params
     * @param string $table Nome da tabela a ser acessada
     * @param array $params Lista dos parâmetros, consulte Query.where()
     * @param int $mode Código do método de retorno, seguinto as constantes da classe PDO
     * @param string|null $classname Nome da classe a ser instanciada, caso $mode seja PDO::FETCH_CLASS
     * @return array|bool
     */
    function selectAllByFields($table, $params, $mode = \PDO::FETCH_OBJ, $classname = null){
        return $this->fetch((new Query())->select()->from($table)->where($params), $mode, $classname);
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
     * Atualiza um registro
     * @param string $table Nome da tabela
     * @param array $params Array de dados a serem atualizados, onde a chave deve ser o nome do campo
     * @param array|int $id Id ou array de ids
     * @return array|bool Retorna TRUE caso a inserção suceda, caso contrário, retorna o array com código e mensagem do erro
     */
    function update($table, $params, $id){
        $q = new Query();
        if(!is_array($id))
            $id = [sprintf(DB_PK_FORMAT, $table), $id];
        return $this->query($q->update($table)->set($params)->where($id));
    }

    /**
     * Deleta um registro da tabela
     * @param string $table Nome da tabela
     * @param array $params Array de parâmetros para a exclusão, baseado na Query::where
     * @return array|\PDOStatement
     */
    function delete($table, $params){
        $q = new Query();
        if(!is_array($params) || empty($params))
            return [-1,-1,"Parâmetros Inválidos"];
        return $this->query($q->delete($table)->where($params));
    }

    /**
     * Executa uma query SQL no banco
     * @param $query
     * @return array|bool
     */
    function run($query){
        $q = $this->handle->prepare($query);
        if($q === false)
            return $this->handle->errorInfo();
        $r = $q->execute();
        if($r === false)
            return [-1, $q->errorCode(), 'Erro ao executar query: '.$q->errorInfo()[2]."<br/>\r\n Query: ".$query];
        return $r;
    }

    /**
     * Obtém o id gerado/acessado na última transação
     * @return string Id
     */
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
     * @param array|\PDOException $errorInfo
     * @param string $query
     * @return false
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
     * Separa um array em um novo array sequencial, contendo um array listando todas as chaves no primeiro membro, e um
     * com todos os valores no segundo
     *
     * Ex: ["a"=>"foo","b"=>"bar"] => [["a","b"],["foo","bar"]]
     * @param $array
     * @return array
     */
    static function keyValSplit($array){
        return [array_keys($array), array_values($array)];
    }
}