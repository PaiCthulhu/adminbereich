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
 * Class CRUDController
 * @package AdmBereich
 */
abstract class CRUDController extends Controller {
    /**
     * @var Model $_model Instância de uma classe Model referente a este controlador
     */
    protected $_model;
    /**
     * @var bool $_redirect ativa ou desativa o redirecionamento após operações no banco de dados
     */
    protected $_redirect;
    /**
     * @var string $_authPrefix Prefixo das permissões associadas a este controlador
     */
    protected $_authPrefix;
    /**
     * @var string $_authFailRedir Endereço que o usuário deverá ser redirecionado caso não possua permissão
     */
    protected $_authFailRedir;

    public $desc, $descPrefix;

    /**
     * Construtor CRUDController
     *
     * Além de instanciar a classe, busca o model relativo a este controlador, e seta as configurações iniciais do
     * controlador.
     * @throws \Exception
     */
    function __construct(){
        parent::__construct();
        $model = DEFAULT_NAMESPACE.'\\Models\\'.$this->getSingular(self::name());
        if(!isset($this->_model) && class_exists($model))
            $this->_model = new $model();
        $this->_redirect = true;
        $this->_authPrefix = strtolower(static::name());
        $this->_authFailRedir = '';
        if(!empty($this->_model))
            $this->desc = $this->_model::name();
        else
            $this->desc = $this->getSingular(self::name());
        $this->descPrefix = 'o';
    }

    /**
     * Lista todos os registros associados
     * @throws \Exception
     */
    function index(){
        $this->authCheck('view');
        if(empty($this->_model))
            throw new \Exception("Nenhum modelo definido para este Controller");
        static::render($this->getView('read'), [strtolower($this->desc).'s' => $this->_model::all()]);
    }

    /**
     * Exibe o formulário para cadastrar um novo registro
     * @throws \Exception
     */
    function add(){
        $this->authCheck('add');
        static::render($this->getView('add'));
    }

    /**
     * Exibe a view do formulário de edição de registros
     * @param $id
     * @throws \Exception
     */
    function edit($id){
        $this->authCheck('edit');
        /**
         * @var Model $model
         */
        $model = new $this->_model();
        $edit = $model::find($id);
        if($edit === false)
            throw new \Exception(strtoupper($this->descPrefix)." {$this->desc} id #{$id} não existe ou não foi encontrad{$this->descPrefix}");
        else
            static::render($this->getView('edit'), [strtolower($this->_model::name())=>$edit]);
    }

    /**
     * Cria um novo registro
     * @return bool
     * @throws \Exception
     */
    function save(){
        $this->authCheck('add');
        if(!isset($this->_model))
            $this->errorHandler("Model não setado");
        if(isset($_POST['_save'])){
            $opt = $_POST;
            unset($opt['_save']);
            $retorno = $this->_model::create($opt);
            if($retorno === true){
                if($this->_redirect)
                    Router::redirect($this->getPath());
                return true;
            }
            else{
                dump($_POST);
                $this->errorHandler("(".$retorno[0].") ".$retorno[2]);
            }
        }
        else{
            dump($_POST);
            $this->errorHandler("Parâmentros Inválidos: ");
        }
        return false;
    }

    /**
     * Atualiza um registro
     * @return bool
     * @throws \Exception
     */
    function update(){
        $this->authCheck('edit');
        if(!isset($this->_model))
            $this->errorHandler("Model não setado");

        $pk = $this->_model->pk();
        if(!isset($_POST[$pk]))
            $this->errorHandler("Id não setado (`{$pk}`)");

        if(isset($_POST['_edit'])){
            $opt = $_POST;
            $id = $opt[$pk];
            unset($opt['_edit'], $opt[$pk]);
            $retorno = $this->_model->update($id, $opt);
            if(is_a($retorno, 'PDOStatement')){
                if($this->_redirect)
                    Router::redirect($this->getPath());
                return true;
            }
            else{
                dump($_POST);
                $this->errorHandler("(".$retorno[0].") ".$retorno[2]);
            }
        }
        else{
            dump($_POST);
            $this->errorHandler("Parâmentros Inválidos: Chave de operação não setada");
        }
        return false;
    }

    /**
     * Deleta um registro, apartir de seu Id
     * @param int $id Id do registro
     * @return bool TRUE caso suceda, FALSE caso contrário
     * @throws \Exception
     */
    function delete($id){
        $this->authCheck('delete');
        if(!isset($this->_model))
            die($this->errorHandler("Model não setado"));

        $retorno = $this->_model::destroy($id);
        if($retorno == true){
            if($this->_redirect)
                Router::redirect($this->getPath());
            return true;
        }
        else
            $this->errorHandler("(".$retorno[0].") ".$retorno[2]);
        return false;
    }

    /**
     * Remove o "S", do final da string
     *
     * Essa função é utilizada para que o Controllers carreguem automaticamente seu Model, que por padrão tem o mesmo
     * nome do Controller, porém no singular. A idéia é expandir esta função para acomodar plurais irregulares da língua
     * portuguesa
     *
     * @param string $string
     * @return string
     */
    function getSingular($string){
        if(substr($string, -1) == 's')
            $string = mb_substr($string, 0, -1);
        return $string;
    }

    /**
     * @param string $mode
     */
    function authCheck($mode){
        if (!Auth::hasPerm($this->_authPrefix.'_'.$mode)){
            Router::redirect($this->_authFailRedir);
            die();
        }
    }
}
