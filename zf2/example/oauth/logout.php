<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

spl_autoload_register(function ($class) { require_once ltrim(str_replace('\\', '/', $class), '/') . '.php'; });

use Zend\Authentication\AuthenticationService;

$auth = new AuthenticationService();

$auth->clearIdentity();

header('Location: index.php');
