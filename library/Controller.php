<?php
/**
 * AdminBereich Framework
 *
 * @link      https://github.com/PaiCthulhu/adminbereich
 * @copyright Copyright (c) 2018-2019 William J. Venancio
 * @license   https://github.com/PaiCthulhu/adminbereich/blob/master/LICENSE.txt (Apache 2.0 License)
 */
namespace AdmBereich;

use eftec\bladeone\BladeOne;

/**
 * Controlador que serve de base para o sistema
 * @package AdmBereich
 */
abstract class Controller{

    /**
     * Constantes de caminhos padrões
     */
    const VIEWS = ROOT.'/app/Views',
          CACHE = ROOT.'/tmp/cache',
          DEFAULT_ROUTE = '';
    /**
     * @var BladeOne $blade Instância do BladeOne
     */
    protected $blade;
    /**
     * @var string $view_folder Caminho para a pasta dos views
     */
    protected $view_folder;

    /**
     * Construtor da classe Controller
     */
    function __construct(){
        $this->blade  = new BladeOne(Controller::VIEWS, Controller::CACHE);
        $this->view_folder = '';
    }

    /**
     * Renderiza a view específicada
     * @param $view
     * @param array $params
     * @return string
     * @throws \Exception Se o parâmetro não for array
     */
    function run($view, $params = array()){
        if(!is_array($params))
            throw new \Exception('Parâmetro não é array');
        $params['_page'] = $view;
        return $this->blade->run($view,$params);
    }

    /**
     * @param $view
     * @param array $params
     * @throws \Exception Se o parâmetro não for array
     */
    function render($view, $params = array()){
        if(!is_array($params))
            throw new \Exception('Parâmetro não é array');
        echo $this->run($view, $params);
    }

    /**
     * Envia a mensagem de erro para a tela
     * @param string $msg
     * @throws \Exception
     */
    function errorHandler($msg){
        throw new \Exception($msg);
    }

    /**
     * Retorna o caminho para este controlador
     * @return string
     */
    function getPath(){
        $retorno = (!empty(static::DEFAULT_ROUTE))?static::DEFAULT_ROUTE:'';
        return $retorno.strtolower($this->name()).'/';
    }

    /**
     * Retorna o caminho de uma view, conforme
     * @param string $mode Qual tela do controlador deverá ser puxado
     * @return string Caminho para a view solicitada
     */
    function getView($mode){
        $folder = (!empty($this->view_folder))? $this->view_folder.'.':'';
        return $folder."pages.".strtolower(static::name()).".{$mode}";
    }

    /**
     * Obtém o nome do controlador (sem namespace)
     * @return string Nome do cotnrolador
     */
    static function name(){
        return substr(static::class, strrpos(static::class, '\\')+1);
    }

}