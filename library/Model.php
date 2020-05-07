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
 * Modelo que serve de base para o sistema
 * @package AdmBereich
 */
abstract class Model{

    /**
     * @var int|string $created Campo padrão que marca a data e hora que o registro foi criado
     */
    public $created;
    /**
     * @var null|int|string $updated Campo padrão que marca a última alteração no registro
     */
    public $updated;
    /**
     * @var DB $db Acesso à instância do DB
     */
    protected $db;
    /**
     * @var string $_table Nome da tabela
     */
    protected $_table;
    /**
     * @var string $_pk Nome da coluna de chave primária
     */
    protected $_pk;
    /**
     * @var array $_columns Lista das colunas
     */
    protected $_columns;

    /**
     * Constructor da classe Model
     *
     * Conecta-se à instância do DB, então seta os nomes padrões da tabela e da chave primária conforme o nome do modelo
     * atual, sendo o nome da tabela igual ao nome do model em letras minúsculas, e o nome padrão da chave primário
     * igual ao nome da tabela concatenado ao sufixo "_id". E.g.: Modelo = "Produto", Nome da tabela = "produto", nome
     * da chave primária = "produto_id"
     */
    function __construct(){
        $this->db = DB::connection();
        $this->_table = strtolower(static::name());
        $this->_pk = sprintf($_ENV['DB_PK_FORMAT'], $this->_table);
        if(!isset($this->created))
            $this->created = date('Y-m-d H:i:s');
    }

    /**
     * Retorna o nome da tabela que o modelo acessa
     * @return string Nome da tabela
     */
    function getTable(){
        return $this->_table;
    }

    /**
     * Retorna o nome da chave primária setada no modelo
     * @return string Nome da chave primária
     */
    function pk(){
        return $this->_pk;
    }

    /**
     * Obtém qual foi o id gerado/acessado na última transação
     * @return string Id
     */
    function lastId(){
        return $this->db->lastId();
    }

    /**
     * Lista todos os registros da tabela
     * @param bool $fetch_class Quando a booleana está ativa, os registro são retornados como instâncias do modelo, ao
     * invés de objetos genéricos
     * @return array|bool FALSE caso nada seja encontrado, senão retorna a listagem
     */
    function getAll($fetch_class = true){
        if($fetch_class)
            return $this->db->selectAll($this->_table, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectAll($this->_table);
    }

    /**
     * Busca por um registro a partir de seu Id
     * @param int $id Id do registro
     * @param bool $fetch_class Quando a booleana está ativa, os registro são retornados como instâncias do modelo, ao
     * invés de objetos genéricos
     * @return bool|\stdClass|static FALSE caso o registro não seja encontrado, senão retorna ou um objeto
     * genérico (\stdClass) ou uma instância do modelo, conforme o parâmentro fetch_class
     */
    function get($id, $fetch_class = true){
        if($fetch_class)
            return $this->db->selectSingle($this->_table, $id, \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectSingle($this->_table, $id);
    }

    /**
     * Busca pelo primeiro registro que possua um valor $value na coluna $field
     * @param string $field Coluna que será usada de parâmetro pra busca
     * @param mixed $value Valor a ser buscado na coluna
     * @param bool $fetch_class Quando a booleana está ativa, os registro são retornados como instâncias do modelo, ao
     * invés de objetos genéricos
     * @return bool|\stdClass|static FALSE caso o registro não seja encontrado, senão retorna ou um objeto
     * genérico (\stdClass) ou uma instância do modelo, conforme o parâmentro fetch_class
     */
    function getByField($field, $value, $fetch_class = true){
        if($fetch_class)
            return $this->db->selectSingleByFields($this->_table, [$field=>$value], \PDO::FETCH_CLASS, static::class);
        else
            return $this->db->selectSingleByFields($this->_table, [$field=>$value]);
    }

    /**
     * Busca todos os registros, ordenados pela coluna $field
     * @param string $field Coluna que servirá de índice para ordenar o retorno
     * @param bool $desc Direção da ordenação: Ascendente caso FALSE, Descendente caso TRUE
     * @param bool $fetch_class Quando a booleana está ativa, os registro são retornados como instâncias do modelo, ao
     * invés de objetos genéricos
     * @return array|bool FALSE caso nada seja encontrado, senão retorna a listagem ordenada
     */
    function getAllOrderBy($field = 'order', $desc = false, $fetch_class = true){
        $q = new Query();
        $mode = ($desc)? 'DESC':'ASC';
        $q->select()->from($this->_table)->orderBy($field, $mode);
        if($fetch_class)
            return $this->fetch($q);
        else
            return $this->run($q);
    }


    /**
     * Busca por todos os registros que satisfaçam as condições fornecidas em $params, e retorna a listagem ordena pela
     * coluna $orderField
     *
     * Para mais detalhes sobre a estrutura de $params, cheque a função Query.where()
     * @param array $params Condições para a busca
     * @param string $orderField Coluna que servirá de índice para ordenar o retorno
     * @param bool $desc Direção da ordenação: Ascendente caso FALSE, Descendente caso TRUE
     * @param bool $fetch_class Quando a booleana está ativa, os registro são retornados como instâncias do modelo, ao
     * invés de objetos genéricos
     * @return array|false FALSE caso nada seja encontrado, senão retorna a listagem da busca ordenada
     */
    function findAllOrderBy($params, $orderField, $desc = false, $fetch_class = true){
        $q = new Query();
        $mode = ($desc)? 'DESC':'ASC';
        $q->select()->from($this->_table)->where($params)->orderBy($orderField, $mode);
        if($fetch_class)
            return $this->fetch($q);
        else
            return $this->run($q);
    }

    /**
     * Atualiza um registro na tabela do modelo, a partir de um id
     * @param int $id Id do registro a ser alterado
     * @param array $params Array de dados a serem inseridos, onde a chave deve ser o nome do campo
     * @return array|true Retorna TRUE caso suceda, do contrário, um array com o erro
     */
    function update($id, $params){
        return $this->db->update($this->_table, $params, [$this->_pk=>$id]);
    }

    /**
     * Salva um registro com os valores conforme os atributos da instância atual do modelo
     * @return array|true Retorna TRUE caso suceda, do contrário, um array com o erro
     * @throws \Exception
     */
    function save(){
        $this->_loadColumns();
        $opt = [];
        foreach ($this->_columns as $column)
            if(isset($this->{$column->name}))
                $opt[$column->name] = $this->columnCheck($column, $this->{$column->name});
        if(isset($opt[$this->_pk]) && !empty($opt[$this->_pk])){
            $id = $opt[$this->_pk];
            unset($opt[$this->_pk]);
            $r = $this->update($id, $opt);
        }
        else {
            unset($opt[$this->_pk]);
            $r = $this->db->insert($this->_table, $opt);
        }

        return $r;
    }

    /**
     * Salva um novo registro com os valores conforme os atributos da instância atual do modelo
     * @return array|true Retorna TRUE caso suceda, do contrário, um array com o erro
     * @throws \Exception
     */
    function saveNew(){
        $this->_loadColumns();
        $opt = [];
        foreach ($this->_columns as $column)
            if(isset($this->{$column->name}))
                $opt[$column->name] = $this->columnCheck($column, $this->{$column->name});
        $r = $this->create($opt);
        return $r;
    }

    /**
     * Deleta o registro da tabela da instância atual do modelo
     * @return array|\PDOStatement
     * @throws \Exception caso não exista um id
     */
    function delete(){
        if(!isset($this->{$this->_pk}))
            throw new \Exception("Id do objeto atual não está definido");
        else
            $id = $this->{$this->_pk};
        return $this->db->delete($this->_table, [$this->_pk=>$id]);
    }

    /**
     * Insere um novo registro na tabela do modelo
     * @param array $params Array de dados a serem inseridos, onde a chave deve ser o nome da coluna
     * @return array|true Retorna TRUE caso suceda, do contrário, um array com o erro
     */
    static function create($params){
        $n = new static();
        return $n->db->insert($n->_table, $params);
    }

    /**
     * Retorna uma instância do modelo preenchida com os valores do registro de id $id
     * @param int $id Id do registro a ser buscado
     * @return static|false FALSE caso o registro não seja encontrado, caso contrário, a instância do modelo com os
     * atributos preenchidos
     */
    static function find($id){
        $n = new static();
        return $n->db->selectSingle($n->_table, $id, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * Retorna a listagem de todos os registros da tabela, instanciados como o modelo
     * @return static[]|false FALSE caso nenhum registro seja encontrado, caso contrário, retorna a listagem dos registros,
     * já instanciados
     */
    static function all(){
        $n = new static();
        return $n->db->selectAll($n->_table, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * Busca por um registro que satisfaça as condições fornecidas em $params
     *
     * Para mais detalhes sobre a estrutura de $params, cheque a função Query.where()
     * @param array $params Condições para a busca
     * @return false|static FALSE caso o registro não seja encontrado, senão retorna uma instância do modelo
     */
    static function first($params){
        $f = new static();
        return $f->db->selectSingleByFields($f->_table, $params, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * Busca pelo último registro da tabela, conforme campo fornecido
     * @param string $field Campo usado de referência para a listagem, padrão "created". Opção seria "id", por exemplo
     * @return static|false
     */
    static function last($field = "created"){
        $l = new static();
        $q = new Query();
        $q->select()->from($l->_table)->orderBy($field, "DESC")->limit(1);
        $r = $l->fetch($q);
        if(!empty($r) && is_array($r))
            return $r[0];
        return $r;
    }

    /**
     * Busca por todos os registros que satisfaçam as condições fornecidas em $params
     *
     * Para mais detalhes sobre a estrutura de $params, cheque a função Query.where()
     * @param array $params Condições para a busca
     * @return false|static[] FALSE caso nada seja encontrado, senão retorna a listagem da busca
     */
    static function where($params){
        $w = new static();
        return $w->db->selectAllByFields($w->_table, $params, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * Busca por todos os registros que satisfaçam as condições fornecidas em $params, e retorna a listagem ordena pela
     * coluna $orderField
     *
     * Para mais detalhes sobre a estrutura de $params, cheque a função Query.where()
     * @param array $param Condições para a busca
     * @param string $orderField Coluna que servirá de índice para ordenar o retorno
     * @param bool $desc Direção da ordenação: Ascendente caso FALSE, Descendente caso TRUE
     * @return static[]|false False caso nada seja encontrado, senão retorna a listagem da busca ordenada
     */
    static function whereOrderBy(array $param, string $orderField, bool $desc = false){
        $w = new static();
        return $w->findAllOrderBy($param, $orderField, $desc, true);
    }

    /**
     * Deleta um ou mais registros da tabela do modelo, a partir dos ids
     * @param int ...$ids Ids dos registros a serem deletados
     * @return array|true TRUE caso nenhum erro acontece, um array com o erro caso contrário
     */
    static function destroy(int ...$ids){
        if(is_array($ids[0]))
            $ids = $ids[0];
        $d = new static();
        foreach ($ids as $id){
            $r = $d->db->delete($d->_table, [$d->_pk=>$id]);
            if(!is_a($r, \PDOStatement::class))
                return $r;
        }
        return true;
    }

    /**
     * Carrega as colunas da tabela do modelo, direto do banco de dados
     * @throws \Exception
     */
    function _loadColumns(){
        $c = $this->db->selectColumns($this->_table);
        if($c === false)
            throw new \Exception('Erro ao carregar colunas do banco de dados');
        $this->_columns = $c;
    }

    /**
     * Busca em uma tabela relacional todos os valores referentes a instância atual do modelo e a instância fornecida
     * em $relClass
     * @param string|Model $relClass Other-Table class
     * @param string $relTable Se for fornecido, usa esse nome de tabela como a tabela relacional, se não combina o nome
     * de ambos os modelos
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
        return $this->run($q);
    }

    /**
     * Busca em uma tabela relacional o valor referente a instância atual do modelo e a instância fornecida em $relClass
     * @param string|Model $relClass
     * @param string $relTable Se for fornecido, usa esse nome de tabela como a tabela relacional, se não combina o nome
     * de ambos os modelos
     * @param string $fk
     * @param string $pk
     * @return Model
     */
    function relSingle($relClass, $relTable = '', $fk = '', $pk = ''){
        if(is_string($relClass))
            $relClass = new $relClass();
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        $q = "SELECT {$fk} FROM {$relTable} WHERE {$pk} = ".$this->{$this->_pk};
        $r = $this->run($q)[0];
        return $relClass::find($r->{$relClass->_pk});
    }

    /**
     * Retorna todos os registros referentes ao modelo atual da tabela relacional
     * @param $relClass
     * @param string $relTable Se for fornecido, usa esse nome de tabela como a tabela relacional, se não combina o nome
     * de ambos os modelos
     * @param string $fk
     * @param string $pk
     * @return array
     */
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
     * Adiciona, em uma tabela relacional, um registro contendo os ids de ambos o modelo atual e o fornecido em $relClass
     * @param Model $relClass
     * @param string $relTable Se for fornecido, usa esse nome de tabela como a tabela relacional, se não combina o nome
     * de ambos os modelos
     * @param string $fk
     * @param string $pk
     * @return array|bool
     */
    function relAttach($relClass, $relTable = '', $fk = '', $pk = ''){
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        /** @noinspection SqlResolve */
        $q = "INSERT INTO {$relTable}(`{$pk}`,`{$fk}`) VALUES ({$this->{$pk}}, {$relClass->{$fk}})";
        return $this->db->run($q);

    }

    /**
     * Remove de uma tabela relacional, registros referentes ao modelo atual e ao modelo fornecido em $relClass
     * @param $relClass
     * @param string $relTable Se for fornecido, usa esse nome de tabela como a tabela relacional, se não combina o nome
     * de ambos os modelos
     * @param string $fk
     * @param string $pk
     * @return array|bool
     */
    function relDetach($relClass, $relTable = '', $fk = '', $pk = ''){
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        $fk = $fk ?: $relClass->_pk;
        /** @noinspection SqlResolve */
        $q = "DELETE FROM {$relTable} WHERE `{$pk}` = {$this->{$pk}} AND `{$fk}` = {$relClass->{$fk}}";
        return $this->db->run($q);
    }

    /**
     * Remove todos os registros de uma tabela relacional referentes ao modelo atual
     * @param $relClass
     * @param string $relTable Se for fornecido, usa esse nome de tabela como a tabela relacional, se não combina o nome
     * de ambos os modelos
     * @param string $pk
     * @return array|bool
     */
    function relDetachAll($relClass, $relTable = '', $pk = ''){
        $relTable = $relTable ?: $this->_table.'_'.$relClass->_table;
        $pk = $pk ?: $this->_pk;
        /** @noinspection SqlResolve */
        $q = "DELETE FROM {$relTable} WHERE `{$pk}` = {$this->{$pk}}";
        return $this->db->run($q);
    }

    /**
     * Executa uma query SQL direto no banco de dados
     * @param string $q
     * @return array|false
     */
    function run($q){
        return $this->db->fetch($q);
    }

    /**
     * Executa uma query SQL de busca e retorna instâncias do Modelo
     * @param string $q
     * @return array|false
     */
    function fetch($q){
        return $this->db->fetch($q, \PDO::FETCH_CLASS, static::class);
    }

    /**
     * Transforma um objeto genérico numa instância de um modelo, a partir dos valores dos atributos
     * @param \stdClass $source Objeto genérico a ser convertido
     * @param string|Model $dest Nome ou instância do modelo que receberá os valores
     * @return Model Modelo preenchido
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
     * Checa e adapta o valor para um válido para a coluna $column
     * @param \stdClass $column Objeto genérico, referente à coluna
     * @param mixed $value Valor que será inserido
     * @return mixed Retorna o valor adaptado para a coluna
     * @throws \Exception
     */
    protected function columnCheck($column, $value = null){
        //Check Null
        if($value === null && $column->cannull == 'NO')
            if($column->name == $this->_pk && $column->index == 'PRI')
                return null;
            else if($column->default == 'CURRENT_TIMESTAMP')
                return $this->created;
            else if(isset($column->default))
                return $column->default;
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
                else if($column->cannull == 'YES' AND $value === null)
                    return null;
                else
                    throw new \Exception("Coluna '{$column->name}' requer um valor de texto, ".Dict::translate(gettype($value))." recebido...");
            //tamanho
            if(strlen($value) > $column->length)
                throw new \Exception("O valor recebido (".strlen($value)." caracteres) ultrapassa o limite de tamanho da coluna '{$column->name}' que é de {$column->length}.");

        }

        return $value;
    }

    /**
     * Formata um número racional para exibição
     * @param float $number Número a ser formatado
     * @param int $decimals Número de casas decimais
     * @return string Retorna o número formatado
     */
    static function numberFormat($number, $decimals = 0){
        return number_format($number, $decimals, ',', '.');
    }

    /**
     * Formata uma data e hora (em string ou timestamp) para exibição
     * @param string|int $timestamp Data e hora a ser formatada
     * @param string $order Ordem de exibição, "date" coloca primeiro a data, depois a hora, "time" coloca primeiro o
     * horário
     * @return false|string FALSE caso ocorra um erro, senão retorna a data e hora formatada
     */
    static function dateTimeFormat($timestamp, $order = 'date'){
        if(is_string($timestamp))
            $timestamp = strtotime($timestamp);
        if($order == 'time')
            return date('H:i:s d/m/Y', $timestamp);
        else
            return date('d/m/Y H:i:s', $timestamp);
    }

    /**
     * Formata uma data (em string ou timestamp) para exibição
     * @param string|int $timestamp Data a ser formatada
     * @return false|string FALSE caso ocorra um erro, senão retorna a data formatada
     */
    static function dateFormat($timestamp){
        if(is_string($timestamp))
            $timestamp = strtotime($timestamp);
        return date('d/m/Y', $timestamp);
    }

    /**
     * Obtém o nome do modelo (sem namespace)
     * @return string Nome do modelo
     */
    static function name(){
        return substr(static::class, strrpos(static::class, '\\')+1);
    }

}