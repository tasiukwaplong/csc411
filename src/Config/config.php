<?php
/**
 * @author: @tasiukwaplong
 * A simple config file for storing environment variables
 * Works with only MYSQL.
 * @usage: DB_CONFIG
 */
require 'messages.php';
$current_env = 'DEV';

$error_report_status = ($current_env === 'DEV') ? 1 : 0; 
error_reporting($error_report_status);

$db_var = [
	'DEV' => [
		'host' => 'localhost',
		'database' => 'srcm',
		'username' => 'root',
		'password' => '',
	],
	'PROD' => [
		'host' => '',
		'database' => '',
		'username' => '',
		'password' => '',
	]
];

define('DB_CONFIG', $db_var[$current_env]);
