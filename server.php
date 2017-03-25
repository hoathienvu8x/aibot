<?php
define('SITE_ROOT', dirname(__FILE__));
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWD', '');
define('DB_NAME', 'aibot');

header('Content-Type:application/json;charset=utf-8');

require_once dirname(__FILE__) . '/aibot.php';

$AIBot = AIBot::init();

$AIBot->run();
exit;