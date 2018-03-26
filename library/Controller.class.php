<?php
use eftec\bladeone;

class Controller{

    const VIEWS = ROOT.DS.'app'.DS.'views',
          CACHE = ROOT.DS.'tmp'.DS.'cache',
          DBLESS = false;
    /**
     * @var bladeone\BladeOne $blade
     * @var Model|null $_model
     * @var bool $admin
     */
    protected $blade, $_model, $admin;

    function __construct(){
        $this->blade  = new eftec\bladeone\BladeOne(Controller::VIEWS, Controller::CACHE);
        $singular = $this->getSingular(get_class($this));
        if(!isset($this->_model) && !static::DBLESS && get_class($this) != 'Controller' && class_exists($singular))
            $this->_model = new $singular();
        $this->admin = false;
    }

    function save(){
        if(!isset($this->_model))
            die($this->errorHandler("Model não setado"));
        if(isset($_POST['_save'])){
            $opt = $_POST;
            unset($opt['_save']);
            $retorno = $this->_model->create($opt);
            if($retorno == true)
                Router::redirect($this->getPath());
            else{
                dump($_POST);
                $this->errorHandler("(".$retorno[0].") ".$retorno[2]);
            }
        }
        else{
            dump($_POST);
            $this->errorHandler("Parâmentros Inválidos: ");
        }

    }

    function update(){
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
            if($retorno == true)
                Router::redirect($this->getPath());
            else{
                dump($_POST);
                $this->errorHandler("(".$retorno[0].") ".$retorno[2]);
            }
        }
        else{
            dump($_POST);
            $this->errorHandler("Parâmentros Inválidos: ");
        }
    }

    function delete($id){
        if(!isset($this->_model))
            die($this->errorHandler("Model não setado"));

        $retorno = $this->_model->delete($id);
        if($retorno == true)
            Router::redirect($this->getPath());
        else
            $this->errorHandler("(".$retorno[0].") ".$retorno[2]);
    }

    /**
     * @param array $params
     * @throws Exception Se o parâmetro não for array
     */
    function run($params = array()){
        $this->render(get_class($this),$params);
    }

    /**
     * @param $view
     * @param array $params
     * @throws Exception Se o parâmetro não for array
     */
    function render($view, $params = array()){
        if(!is_array($params))
            throw new Exception('Parâmetro não é array');
        $params['_page'] = $view;
        echo $this->blade->run($view,$params);
    }

    function errorHandler($msg){
        throw new Exception($msg);
    }

    function getSingular($string){
        if(substr($string, -1) == 's')
            $string = mb_substr($string, 0, -1);
        return $string;
    }

    function getPath(){
        $retorno = '';
        if($this->admin)
            $retorno = 'admin/';
        return $retorno.get_class($this).'/';
    }

}