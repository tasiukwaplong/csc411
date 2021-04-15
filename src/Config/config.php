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
		'database' => 'abc_insurance',
		'username' => 'root',
		'password' => '',
		'paystack_private_key' => 'sk_live_e20ed6aa097e165a297ad09207e30896597c24e8',
	],
	'PROD' => [
		'host' => '',
		'database' => '',
		'username' => '',
		'password' => '',
		'paystack_private_key' => '',
	]
];

define('DB_CONFIG', $db_var[$current_env]);
