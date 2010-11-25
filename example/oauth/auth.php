<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

require_once 'Zend/Config.php';
$config = new Zend_Config(require_once 'config.php');

require_once 'Zend/Auth.php';
require_once 'Ja/Auth/Adapter/Oauth.php';
require_once 'Zend/Oauth/Consumer.php';

$options = $config->toArray();
$consumer = new Zend_Oauth_Consumer($options);

$adapter = new Ja_Auth_Adapter_Oauth();
$adapter->setConsumer($consumer);

if (isset($_GET['oauth_token'])) {
    $adapter->setQueryData($_GET);
}

$auth = Zend_Auth::getInstance();

if ($auth->hasIdentity()) {
    header("Location: index.php");
}

$result = $auth->authenticate($adapter);

if ($result->isValid()) {
    header('Location: index.php');
} else {
    echo "Error authenticating: " . implode('<br />', $result->getMessages());
}
