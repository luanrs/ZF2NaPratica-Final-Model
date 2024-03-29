<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
date_default_timezone_set('America/Sao_Paulo');

chdir(dirname(__DIR__));

// Setup autoloading
include 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(include 'config/application.config.php')->run();

