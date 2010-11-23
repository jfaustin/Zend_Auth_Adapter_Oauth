<?php
set_include_path(realpath(dirname(__FILE__) . '/../library'));

require_once 'Zend/Auth.php';

$auth = Zend_Auth::getInstance();

if ($auth->hasIdentity()) {
    echo "Logged into Twitter as user " . $auth->getIdentity();
} else {
    echo '<a href="auth.php">Authenticate with Twitter</a>';
}
