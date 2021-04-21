<?php
/**
 * @author: @tasiukwaplong
 */

$messages_var = [
	'connection_issue' => 'Sorry, connection to database not possible',
	'table_name_not_set' => 'Table name not set. kindly pass it as parameter',
	'env_not_set'=>'Environment variables not set. kindly refer to the documentation',
	'no_data_yet'=>'No data yet',
	'format_not_correct' => 'Input format not correct.',
	'data_inserted' => 'Data has been inserted successfully',
	'data_not_added' => 'Could not add data. Try again',
	'data_updated' => 'Data updated successfully',
	'no_data_updated' => 'No data was updated. Search did not return any result',
	'data_deleted' => 'Delete operation successful.',
	'no_data_deleted' => 'Could not delete. Search did not return any result',
	'query_error' => 'The query run seem not to be correct',
	'paystack_key_error'=>'An error occured trying to connect to paystack. Kindly provide a valid private key',
	'insert_input_incomplete'=>'Input not complete. Could not add user.',
	'user_add_error'=>'Unable to add user. Try again',
	'user_add_success'=>'User account created successfully. Kindly check your email to complete your registration',
	'email_or_password_empty'=>'Email or password field is empty. Login not possible',
	'login_incorrect'=>'Could not log user in. Try again using correct username and password',
	'account_exists'=>'User with same email already exist..!!',
	'password_mismatch'=>'Passwords do not match. Try again',
	'password_change_not_possible'=>'Sorry, could not change your password.',
	'password_changed' => 'Password changed successfully',
	'user_logged_out' => 'User logged out successfully',
	'operation_not_success' => 'Operation not successful. Try again',
	'inavalid_auth' => 'Could not authenticate you. Link already expired or link',
	'already_verified' => 'Account already authenticated.',
	'auth_success'=> 'Account successfully verified. You can proceed to log in now',
	'user_not_auth'=>'Could not login. User not yet authenticated'
];

define('GENREAL_MESSAGES', $messages_var);
