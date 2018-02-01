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
        echo $this->blade->run(get_class($this),$params);
    }

}