<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

spl_autoload_register(function ($class) { require_once ltrim(str_replace('\\', '/', $class), '/') . '.php'; });

use Zend\Authentication\AuthenticationService;

$auth = new AuthenticationService();

if ($auth->hasIdentity()) {
    echo "Logged into OAuth source.  Identity is: <Br /><pre>" . print_r($auth->getIdentity(), true) . "</pre>";
    echo "<br /><br />";
    echo '<a href="logout.php">Logout</a>';
} else {
    echo '<a href="auth.php">Authenticate with OAuth Source</a>';
}
