<?php
$routes = new Router();

$routes->get('', 'Site/home');
$routes->get('admin', 'Admin/index');
$routes->get('admin/login', 'Admin/logar');
$routes->get('admin/logout', 'Admin/sair');