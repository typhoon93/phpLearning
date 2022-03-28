<?php

declare(strict_types = 1);

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR . "public_html" . DIRECTORY_SEPARATOR;

define('APP_PATH', $root . 'app' . DIRECTORY_SEPARATOR);
define('FILES_PATH', $root . 'transaction_files' . DIRECTORY_SEPARATOR);
define('VIEWS_PATH', $root . 'views' . DIRECTORY_SEPARATOR);



/* YOUR CODE (Instructions in README.md) */

require(APP_PATH . DIRECTORY_SEPARATOR . "app.php");


$files = get_dir_files(FILES_PATH);
$contents = dir_csv_contents($files);


require(VIEWS_PATH . DIRECTORY_SEPARATOR . "transactions.php");

