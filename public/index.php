<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Setup autoloading
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require 'init_autoloader.php';
require_once $root.'/crm/crm-admision/module/Sistema/src/MyClasses/Utils.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
