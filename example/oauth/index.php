<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

require_once 'Zend/Auth.php';

$auth = Zend_Auth::getInstance();

if ($auth->hasIdentity()) {
    echo "Logged into OAuth source.  Identity is: <Br /><pre>" . print_r($auth->getIdentity(), true) . "</pre>";
    echo "<br /><br />";
    echo '<a href="logout.php">Logout</a>';
} else {
    echo '<a href="auth.php">Authenticate with OAuth Source</a>';
}
