<?php

// Update the include path to add your project's root directory. This allows using require/include with relative paths from the root.
set_include_path(get_include_path() . PATH_SEPARATOR . 'C:\xampp\htdocs\children_games');

// Define the base path of the project only once
define('BASE_PATH', realpath(__DIR__));

// Database configurations
define('DB_HOST', 'localhost');
define('DB_NAME', 'children_games');
define('DB_USER', 'root');
define('DB_PASS', '');

// Include the database connection script
// Using BASE_PATH ensures that the path is always correctly resolved, regardless of where config.php is included from.
require_once BASE_PATH . '/includes/database.php';
