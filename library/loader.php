<?php
/**
 * Arquivo central que puxa todas as dependências para rodar o site
 */
require_once(ROOT.'/config/config.php');     //Puxa as configurações
require ROOT.'/vendor/autoload.php';                //Carrega as dependências
require_once(ROOT.'/library/shared.php');    //

require_once(ROOT.'/config/routes.php');     //Carrega a classe de rotas e suas configurações

if(DEBUG)                                           //Se estiver no modo desenvolvimento, checa se algum arquivo .sass precisa ser compilado
    \AdmBereich\Sass::compile(ROOT."/public/sass/", ROOT."/public/css/", '\Leafo\ScssPhp\Formatter\Compressed');

$app = new AdmBereich\App();
$app->setRouter($routes)->run();