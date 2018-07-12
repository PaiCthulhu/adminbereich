<?php

namespace AdmBereich;

class CRUDController extends Controller {
    /**
     * @var Model $_model
     * @var bool $_redirect
     * @var string $_authPrefix
     * @var string $_authFailRedir
     * @var string $view_folder
     */
    protected $_model, $_redirect, $_authPrefix, $_authFailRedir, $view_folder;
    public $desc, $descPrefix;

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
        $this->view_folder = '';
    }

    /**
     * @throws \Exception
     */
    function index(){
        $this->authCheck('view');
        static::render($this->getView('read'), [strtolower($this->desc).'s' => $this->_model::loadAll()]);
    }

    /**
     * @throws \Exception
     */
    function add(){
        $this->authCheck('add');
        static::render($this->getView('add'));
    }

    /**
     * @param $id
     * @throws \Exception
     */
    function edit($id){
        $this->authCheck('edit');
        /**
         * @var Model $model
         */
        $model = new $this->_model();
        $edit = $model::load($id);
        if($edit === false)
            throw new \Exception(strtoupper($this->descPrefix)." {$this->desc} id #{$id} não existe ou não foi encontrad{$this->descPrefix}");
        else
            static::render($this->getView('edit'), [strtolower($this->_model::name())=>$edit]);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    function save(){
        $this->authCheck('add');
        if(!isset($this->_model))
            die($this->errorHandler("Model não setado"));
        if(isset($_POST['_save'])){
            $opt = $_POST;
            unset($opt['_save']);
            $retorno = $this->_model->create($opt);
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
     * @return bool
     * @throws \Exception
     */
    function update(){
        $this->authCheck('edit');
        if(!isset($this->_model))
            die($this->errorHandler("Model não setado"));

        $pk = $this->_model->pk();
        if(!isset($_POST[$pk]))
            die($this->errorHandler("Id não setado (`{$pk}`)"));

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
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    function delete($id){
        $this->authCheck('delete');
        if(!isset($this->_model))
            die($this->errorHandler("Model não setado"));

        $retorno = $this->_model->delete($id);
        if($retorno == true){
            if($this->_redirect)
                Router::redirect($this->getPath());
            return true;
        }
        else
            $this->errorHandler("(".$retorno[0].") ".$retorno[2]);
        return false;
    }


    function getView($mode){
        $folder = (!empty($this->view_folder))? $this->view_folder.'.':'';
        return $folder."pages.".strtolower(static::name()).".{$mode}";
    }

    function getSingular($string){
        if(substr($string, -1) == 's')
            $string = mb_substr($string, 0, -1);
        return $string;
    }

    function authCheck($mode){
        if (!Auth::hasPerm($this->_authPrefix.'_'.$mode)){
            Router::redirect($this->_authFailRedir);
            die();
        }
    }
}