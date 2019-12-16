<?php
/**
 * Arquivo central que puxa todas as dependências para rodar o site
 */
require_once(ROOT.'/config/config.php');     //Puxa as configurações
require ROOT.'/vendor/autoload.php';         //Carrega as dependências

$app = new AdmBereich\App();                //Instancia o aplicativo

require_once(ROOT.'/library/shared.php');    //Configura definições de ini, além de funções padrão
require_once(ROOT.'/config/routes.php');     //Carrega a classe de rotas e suas configurações

if($_ENV['APP_DEBUG'])                       //Se estiver no modo desenvolvimento, checa se algum arquivo .sass precisa ser compilado
    \AdmBereich\Sass::compile(ROOT."/public/sass/", ROOT."/public/css/", '\ScssPhp\ScssPhp\Formatter\Compressed');

$app->setRouter($routes)->run();