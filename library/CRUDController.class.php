<?php

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
        $singular = $this->getSingular(get_class($this));
        if(!isset($this->_model) && class_exists($singular))
            $this->_model = new $singular();
        $this->_redirect = true;
        $this->_authPrefix = strtolower(get_class($this->_model)).'s';
        $this->_authFailRedir = PATH;
        $this->desc = $this->_model;
        $this->descPrefix = 'o';
        $this->view_folder = '';
    }

    /**
     * @throws Exception
     */
    function add(){
        $this->authCheck('add');
        static::render($this->getView('add'));
    }

    /**
     * @param $id
     * @throws Exception
     */
    function edit($id){
        $this->authCheck('edit');
        /**
         * @var Model $model
         */
        $model = new $this->_model();
        $edit = $model::load($id);
        if(!$edit)
            throw new Exception(strtoupper($this->descPrefix)." {$this->desc} id #{$id} não existe ou não foi encontrad{$this->descPrefix}");
        else
            static::render($this->getView('edit'), [strtolower(get_class($this->_model))=>$edit]);
    }

    function save(){
        $this->authCheck('add');
        if(!isset($this->_model))
            die($this->errorHandler("Model não setado"));
        if(isset($_POST['_save'])){
            $opt = $_POST;
            unset($opt['_save']);
            $retorno = $this->_model->create($opt);
            if($retorno == true){
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
     * @throws Exception
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
            $this->errorHandler("Parâmentros Inválidos: ");
        }
        return false;
    }

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
        return $folder."pages.".strtolower(get_class($this->_model))."s.{$mode}";
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