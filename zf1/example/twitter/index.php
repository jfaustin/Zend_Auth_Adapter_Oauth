<?php
set_include_path(realpath(dirname(__FILE__) . '/../../library'));

require_once 'Zend/Auth.php';

$auth = Zend_Auth::getInstance();

if ($auth->hasIdentity()) {
    $identity = $auth->getIdentity();
    
    echo "Logged into Twitter as user " . $identity['screen_name'];
    echo "<br /><br />";
    echo '<a href="logout.php">Logout</a>';    
} else {
    echo '<a href="auth.php">Authenticate with Twitter</a>';
}
