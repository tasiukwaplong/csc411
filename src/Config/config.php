<?php
// sk_live_e20ed6aa097e165a297ad09207e30896597c24e8
/**
 * A simple config file for storing environment variables
 * Works with only MYSQL.
 * @usage: DB_CONFIG
 */
require 'messages.php';
$current_env = 'PROD';

$error_report_status = ($current_env === 'DEV') ? 1 : 0; 
error_reporting($error_report_status);

$db_var = [
	'DEV' => [
		'host' => 'localhost',
		'database' => 'abc_insurance',
		'username' => 'root',
		'password' => '',
		'paystack_private_key' => 'sk_test_f62006c100fd5fbd8aa7c980f4281f13d256c9f3',
		'frontend_url' => 'https://frontend.com',
		'backend_url' => 'https://localhost/csc411/',
	],
	'PROD' => [
		'host' => 'localhost',
		'database' => 'id16659254_abc_insurance',
		'username' => 'id16659254_root_admin',
		'password' => '##7277Password',
		'paystack_private_key' => '',
		'frontend_url' => 'https://frontend.com',
		'backend_url' => 'https://localhost/csc411/',
	]
];

define('DB_CONFIG', $db_var[$current_env]);
