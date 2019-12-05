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
 * Classe auxiliar usada para gerar queries SQL de forma mais prática e/ou eficaz
 * @package AdmBereich
 */
class Query{

    /**
     * @var string $query Query SQL atual da instância
     */
    protected $query;

    const ORDER_BY_RANDOM = 'RAND()';

    /**
     * Função padrão do PHP que é executada quando a classe é usada como uma string; por exemplo, ao concatenar o objeto
     * com um texto
     * @return string
     */
    function __toString(){
        return $this->getSQL();
    }

    /**
     * Obtém a query SQL atual do objeto
     * @return string Query SQL
     */
    function getSQL(){
        return $this->query;
    }

    /**
     * Gera a declaração SQL INSERT INTO, onde se fornecido um array, cria a lista das colunas afetados
     * @param string $table Nome da tabela onde ocorrerá a inserção
     * @param array $fields Se preenchido, gera a lista de colunas afetadas
     * @return Query $this Retorna a própria instância para encadeamento de métodos
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
     * Gera a declaração complementar VALUES, gerando a lista dos valores a partir de um array
     * @param array $params
     * @return Query $this Retorna a própria instância para encadeamento de métodos
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
     * Gera a primeira parte da declaração SQL UPDATE
     * @param string $table Nome da tabela a ser atualizada
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function update($table){
        $this->query = "UPDATE `{$table}` ";
        return $this;
    }

    /**
     * Gera o corpo da declaração SQL UPDATE, preenchendo colunas e valores conforme as chaves e valores do array de
     * parâmetros
     * @param array $params Array associativo, contendo as colunas como chaves
     * @return Query $this Retorna a própria instância para encadeamento de métodos
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
     * Gera a declaração SQL DELETE FROM
     * @param string $table Nome da tabela que terá o(s) registro(s) deletado(s)
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function delete($table){
        $this->query = "DELETE FROM `{$table}` ";
        return $this;
    }

    /**
     * Gera a primeira parte da declaração SQL SELECT
     * @param array $fields Se fornecido, cria a lista dos campos que serão buscados
     * @return Query $this Retorna a própria instância para encadeamento de métodos
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
     * Gera a declaração auxiliar FROM
     * @param string|array $table Nome da tabela
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function from($table){
        if(is_array($table))
            $this->query .= "FROM `{$table[0]}`.`{$table[1]}` ";
        else
            $this->query .= "FROM `{$table}` ";
        return $this;
    }

    /**
     * Gera a declaração SQL WHERE, conforme o array de condições fornecido
     *
     * Os parâmetros devem ser um array associativo, onde a chave é o nome da coluna e seu valor, o valor a ser buscado.
     * Ex: ['categoria_id'=>2, 'ativo'=>true] => "WHERE `categoria_id` = 2 AND `ativo` = 1"
     *
     * Você pode substituir o valor por um array sequencial para fazer condicionais.
     * Ex: ['valor'=>['>=',20]] => "WHERE `valor` >= 20"
     *
     * @param array $conds Condições para a busca
     * @param string $mode Operador de concatenação lógica das condições, pode ser AND ou OR
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function where($conds = [], $mode = 'AND'){
        $q = "WHERE ";
        if(!empty($conds)){
            if(!$this->isAssoc($conds) AND count($conds) == 2 AND is_string($conds[0]))
                $q .= "`{$conds[0]}` = ".$this->valueEscape($conds[1]);
            else
                foreach ($conds as $key=>$row){
                    if(!is_array($row))
                        if(is_null($row))
                            $q .= "`{$key}` IS ".$this->valueEscape($row);
                        else
                            $q .= "`{$key}` = ".$this->valueEscape($row);
                    else{
                        if(count($row) == 4){
                            if($row[3] === false)
                                $q .= "`$row[0]` {$row[1]} $row[2]";
                            else
                                $q .= "$row[0] {$row[1]} ".$this->valueEscape($row[2]);

                        }
                        else if(count($row) == 3)
                            $q .= "`$row[0]` {$row[1]} ".$this->valueEscape($row[2]);
                        elseif(count($row) == 2)
                            if($row[0] == "BETWEEN" && is_array($row[1]))
                                $q.= "`{$key}` {$row[0]} ".implode(" AND ", $this->valueEscape($row[1]));
                            else
                                $q .= "`{$key}` {$row[0]} ".$this->valueEscape($row[1]);
                    }
                    $q.= ($key !== $this->arrayLastKey($conds))?" {$mode} ":" ";
                }
        }
        else
            $q .= "1";
        $this->query .= $q." ";
        return $this;
    }

    /**
     * Adiciona a palavra-chave SQL ORDER BY, para ordenar os resultados
     * @param string|array $field Campo ou campos que serão usados de índice para a ordenação
     * @param string $ord Direção da ordenação, pode ser ASC para ascendente ou DESC para descendente
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function orderBy($field, $ord = 'ASC'){
        $q = "ORDER BY ";
        if($field == self::ORDER_BY_RANDOM)
            $q .= "RAND()";
        else if(is_array($field)){
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
     * Cláusula de limite, usada no MySQL para delimitar o tamanho do conjunto retornado
     * @param int|array $limit Número máximo de registros retornados
     * @param int $offset Desloca o início da contagem pra outro ponto da lista, muito útil para paginação
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function limit($limit, $offset = 0){
        if(is_array($limit))
            $q = " LIMIT {$limit[0]}, {$limit[1]}";
        else{
            $q = " LIMIT {$limit}";
            if(!empty($offset))
                $q .= " OFFSET {$offset}";
        }
        $this->query .= $q;
        return $this;
    }

    /**
     * Gera a declaração SQL SHOW TABLES
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function showTables(){
        $this->query = "SHOW TABLES ";
        return $this;
    }

    /**
     * Gera a declaração SQL SHOW
     * @param string $index
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     *
     */
    function showIndex($index = 'INDEX'){
        $this->query = "SHOW {$index} ";
        return $this;
    }

    /**
     * Aquela exceção.... adiciona manualmente strings à Query
     * @param string $string
     * @return Query $this Retorna a própria instância para encadeamento de métodos
     */
    function extra($string){
        $this->query .= $string;
        return $this;
    }

    /**
     * "Escapa" valores para serem recebidos de maneira segura pela Query SQL, prezando pela tipagem original do valor
     * @param mixed $val Valor que será inserido no banco de dados
     * @return false|mixed Retorna false caso o valor não seja um tipo válido senão retorna o valor convetido para uma
     * Query SQL
     */
    protected function valueEscape($val){
        if(is_null($val))
            return 'NULL';
        else if(is_bool($val))
            return ($val)?'TRUE':'FALSE';
        else if(is_string($val)){
            if($val == "\0")
                return 'NULL';
            else if(preg_match('/^\:[^\s\:]*(?<!:)/', $val) == 1 && $val != ':') //Check if :param
                return $val;
            else
                return "'".str_replace("'", "\\'", $val)."'";
        }
        else if(is_int($val))
            return $val;
        else if(is_array($val)){
            $new = [];
            foreach ($val as $key=>$item) {
                $new[$key] = $this->valueEscape($item);
            }
            return $new;
        }
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
     * Remove todas as chaves vazias de um array
     * @param array $array
     * @return array Retorna o array filtrado
     */
    protected function arrayClearEmpty($array){
        return array_filter($array, function ($value) {
            return isset($value) && $value !== null;
        });
    }

    /**
     * Retorna a última chave de um array
     * @param array $array
     * @return int|string|null Retorna a posição em um array indexado, o nome da chave no caso de um array associativo
     * ou null no caso da variável fornecida não ser um array
     */
    protected function arrayLastKey($array){
        end($array);
        return key($array);
    }
}