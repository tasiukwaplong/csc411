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
];

define('GENREAL_MESSAGES', $messages_var);
