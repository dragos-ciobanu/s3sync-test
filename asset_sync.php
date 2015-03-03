<?php

define('APP_ROOT', dirname(__FILE__));

// all secret credentials
define('AWS_ACCESS_KEY_ID', 'AKIAI7VZ5LNDNKCENAOA');
define('AWS_SECRET_ACCESS_KEY', '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');
define('AWS_BUCKET', 'great-bucket-yeah');
define('AWS_PREFIX', 'assets');
// current directory
defined('LOCALPATH') || define('LOCALPATH', 'assets');

// enable autoload for vendor software
include 'vendor/autoload.php';

set_include_path(get_include_path() . PATH_SEPARATOR . APP_ROOT . '/lib');
spl_autoload_register(
    function ($className) {
	spl_autoload(str_replace("\\", "/", $className));
    }
);

/**
 * Options for S3
 */
$options = new StdClass;
$options->key = AWS_ACCESS_KEY_ID;
$options->secret = AWS_SECRET_ACCESS_KEY;
// target bucket
$options->bucket = AWS_BUCKET;
// name of directory - i.e. assets/
$options->prefix = AWS_PREFIX;

$store = new RocketInternet\S3Storage($options);
$sync = new RocketInternet\AssetSync($store);
$sync->sync(LOCALPATH);
