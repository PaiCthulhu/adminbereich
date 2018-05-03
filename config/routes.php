<?php
$routes = new \AdmBereich\Router();
$routes->namespace = DEFAULT_NAMESPACE;

$routes->get('', 'Site/home');
$routes->get('admin', 'Admin/index');
$routes->get('admin/login', 'Admin/logar');
$routes->get('admin/logout', 'Admin/sair');