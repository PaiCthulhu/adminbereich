<?php
require_once(ROOT.DS.'config'.DS.'config.php');
require_once(ROOT.DS.'library'.DS.'shared.php');

require_once(ROOT.DS.'vendor'.DS.'BladeOne'.DS.'BladeOne.php');

require_once(ROOT.DS.'config'.DS.'routes.php');

$routes->route($_GET['url']);