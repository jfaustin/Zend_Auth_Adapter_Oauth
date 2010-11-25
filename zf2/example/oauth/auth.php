<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

spl_autoload_register(function ($class) { require_once ltrim(str_replace('\\', '/', $class), '/') . '.php'; });

use Zend\Authentication\AuthenticationService as AuthenticationService;
$auth = new AuthenticationService();

use Zend\Config\Config as Config;
$config = new Config(require_once 'config.php');
$options = $config->toArray();

use Zend\Oauth\Consumer as OauthConsumer;
$consumer = new OauthConsumer($options);

use Ja\Authentication\Adapter\Oauth as OauthAdapter;
$adapter = new OauthAdapter();
$adapter->setConsumer($consumer);

if (isset($_GET['oauth_token'])) {
    $adapter->setQueryData($_GET);
}

if ($auth->hasIdentity()) {
    header("Location: index.php");
}

$result = $auth->authenticate($adapter);

if ($result->isValid()) {
    header('Location: index.php');
} else {
    echo "Error authenticating: " . implode('<br />', $result->getMessages());
}
