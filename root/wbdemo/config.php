<?php

define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'wbdemo');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', 'wbdemo');
define('TABLE_PREFIX', 'wb_');

define('WB_URL', 'http://localhost:4001/wbdemo');
define('ADMIN_DIRECTORY', 'admin'); // no leading/trailing slash or backslash!! A simple directory only!!

require_once(dirname(__FILE__).'/framework/initialize.php');

