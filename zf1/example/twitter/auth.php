<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

require_once 'Zend/Config.php';
$config = new Zend_Config(require_once 'config.php');

require_once 'Zend/Auth.php';
require_once 'Ja/Auth/Adapter/Oauth/Twitter.php';

$adapter = new Ja_Auth_Adapter_Oauth_Twitter();
$adapter->setConsumerKey($config->consumerKey)
        ->setConsumerSecret($config->consumerSecret)
        ->setCallbackUrl($config->callbackUrl);

if (isset($_GET)) {
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
