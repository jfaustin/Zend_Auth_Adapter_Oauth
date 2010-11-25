<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

require_once 'Zend/Auth.php';

$auth = Zend_Auth::getInstance();

$auth->clearIdentity();

header('Location: index.php');
