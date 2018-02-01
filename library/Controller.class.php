<?php
use eftec\bladeone;

class Controller{

    const VIEWS = ROOT.DS.'app'.DS.'views',
          CACHE = ROOT.DS.'tmp'.DS.'cache';
    protected $blade;

    function __construct(){
        $this->blade  = new eftec\bladeone\BladeOne(Controller::VIEWS, Controller::CACHE);
    }

    function run($params = array()){
        $this->render(get_class($this),$params);
    }

    function render($view, $params = array()){
        echo $this->blade->run($view,$params);
    }

}