<?php
define('HTTP', $_SERVER['HTTP_HOST'].str_replace('/admin', '',dirname($_SERVER['PHP_SELF'])));
// HTTP
define('HTTP_SERVER', 'http://'.HTTP.'/admin/');
define('HTTP_CATALOG', 'http://'.HTTP.'/');

// HTTPS
define('HTTPS_SERVER', 'http://'.HTTP.'/admin/');
define('HTTPS_CATALOG', 'http://'.HTTP.'/');

// DIR
define('DIR_APPLICATION', '/opt/lampp/htdocs/ogawa/admin/');
define('DIR_SYSTEM', '/opt/lampp/htdocs/ogawa/system/');
define('DIR_IMAGE', '/opt/lampp/htdocs/ogawa/image/');
define('DIR_LANGUAGE', '/opt/lampp/htdocs/ogawa/admin/language/');
define('DIR_TEMPLATE', '/opt/lampp/htdocs/ogawa/admin/view/template/');
define('DIR_CONFIG', '/opt/lampp/htdocs/ogawa/system/config/');
define('DIR_CACHE', '/opt/lampp/htdocs/ogawa/system/storage/cache/');
define('DIR_DOWNLOAD', '/opt/lampp/htdocs/ogawa/system/storage/download/');
define('DIR_LOGS', '/opt/lampp/htdocs/ogawa/system/storage/logs/');
define('DIR_MODIFICATION', '/opt/lampp/htdocs/ogawa/system/storage/modification/');
define('DIR_UPLOAD', '/opt/lampp/htdocs/ogawa/system/storage/upload/');
define('DIR_CATALOG', '/opt/lampp/htdocs/ogawa/catalog/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'ogawa');
define('DB_PORT', '3307');
define('DB_PREFIX', 'oc_');
