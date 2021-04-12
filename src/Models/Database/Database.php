<?php

/**
 * @author: @tasiukwaplong
 */
require '../../Config/config.php';

class Database extends mysqli{
	public $status = ['errored'=> false, 'msg'=> ''];
	private $table;
	private $customQuery;
	private $result;

	function __construct($options=['port'=>0, 'table'=>'']){
		if (!$this->envVarIsSet()) $this->setStatus(true, GENREAL_MESSAGES['env_not_set']);
		if(!empty($options['table'])) $this->table = $options['table'];
		parent::__construct(
			DB_CONFIG['host'], DB_CONFIG['username'],
			DB_CONFIG['password'],DB_CONFIG['database'], $options['port']
		);
		if (mysqli_connect_errno()) $this->setStatus(true, mysqli_connect_error());
	}

	public function table($tableName = null){
		if(is_null($tableName)) $this->setStatus(true, GENREAL_MESSAGES['table_name_not_set']);
		else $this->table = $tableName;
		return $this;
	}

	public function fetch( $selectQuery = null){
	  // handles SELECT statents
	  if(is_null($selectQuery)) $this->customQuery = 'SELECT * FROM '.$this->table;
	  else $this->customQuery = str_replace('{table}', $this->table, $selectQuery);	  
	  return $this->runQuery()->fetchResults(); 
	}

	private function fetchResults(){		($this->result->num_rows < 1) ? $this->setStatus(true, GENREAL_MESSAGES['no_data_yet']) : $this->setStatus(false, $this->result->fetch_assoc());
		return $this->status;
	}

	private function fetchAsMultiple(){
		if ($this->status['errored']) return $this->status;
		$result = [];
		for ($i=0; $i < $this->result->num_rows; $i++) { 
			array_push($result, $this->result->fetch_assoc());
		}
		$this->setStatus(false, $result);
		return $this->status;
	}

	public function insert($options = ''){
	  // run insert statement
	  if ($this->status['errored']) return $this->status;
	  if (gettype($options) !== 'array' || count($options) < 1){
        $this->setStatus(true, GENREAL_MESSAGES['format_not_correct']);
        return $this->status;
      }

      $columns = implode(',',array_keys($options));
      $values = implode(',', $this->surroundWithQuotes(array_values($options)));
      $this->customQuery = str_replace('{table}', $this->table, "INSERT INTO ".$this->table." ($columns) VALUES($values)");

      $result = $this->runQuery()->result;
	  if ($this->status['errored']) return $this->status;

      if ($this->affected_rows >= 1) $this->setStatus(false, GENREAL_MESSAGES['data_inserted']);
      return $this->status;
	}

	public function update($updateQuery = null){
      // run sql update command
      if (is_null($updateQuery) || strlen($updateQuery) < 10) $this->setStatus(true, GENREAL_MESSAGES['format_not_correct']);
      
      if ($this->status['errored']) return $this->status;
	  else $this->customQuery = str_replace('{table}', $this->table, $updateQuery);	  

      if ($this->runQuery()->status['errored']) return $this->status;

      ($this->affected_rows >= 1) ? $this->setStatus(false, GENREAL_MESSAGES['data_updated']) : $this->setStatus(true, GENREAL_MESSAGES['no_data_updated']);
      return $this->status;
    }

    public function delete($deleteQuery = null){
      // run delete sql command
      if (is_null($deleteQuery) || strlen($deleteQuery) < 10) $this->setStatus(true, GENREAL_MESSAGES['format_not_correct']);
      
      if ($this->status['errored']) return $this->status;
	  else $this->customQuery = str_replace('{table}', $this->table, $deleteQuery);	  

      if ($this->runQuery()->status['errored']) return $this->status;

      ($this->affected_rows >= 1) ? $this->setStatus(false, GENREAL_MESSAGES['data_deleted']) : $this->setStatus(true, GENREAL_MESSAGES['no_data_deleted']);
      return $this->status;
    }

    public function rawQuery($rawQuery = null){
      // run delete sql command
      if (is_null($rawQuery) || strlen($rawQuery) < 10) $this->setStatus(true, GENREAL_MESSAGES['format_not_correct']);
      
      if ($this->status['errored']) return $this->status;
	  else $this->customQuery = str_replace('{table}', $this->table, $rawQuery);	  

      if ($this->runQuery()->status['errored']) return $this->status;

      ($this->result) ? $this->setStatus(false, $this) : $this->setStatus(true, GENREAL_MESSAGES['query_error']);
      return $this->status;
    }

	private function runQuery(){
	  // run sql query
	  if ($this->status['errored']) return $this->status;
	  $result = parent::query($this->customQuery);
	  
	  if(!$result) $this->setStatus(true, mysqli_error($this));
	  else $this->result = $result;	
	  return $this;
	}

	private function envVarIsSet(){
	  // check to see if all environmental variables are set
	  if(!DB_CONFIG || gettype(DB_CONFIG) !== 'array') return false;
	  $arrayDiff = array_diff(
	  	array_keys(DB_CONFIG),
	  	['host', 'database', 'username', 'password'] // must contain these keys
	  );
	  return (count($arrayDiff) === 0);
	}

	private function surroundWithQuotes($arrayedData){
      // surround with quote
      $escapedData = [];
      for ($i=0; $i < count($arrayedData); $i++) { 
        array_push($escapedData, "'".$this->cleanStr($arrayedData[$i])."'");
      }
      return $escapedData;
    }

    private function cleanStr($options){
     // perform other string filtering
      return htmlspecialchars(strip_tags(stripcslashes($options)));
    }

	private function setStatus($status = true, $message = 'An unidentified error just occured. ERR_500'){
	  // set $status errored and message if any 
	  if (gettype($status) !== 'boolean') $this->status['errored'] = true;
	  $this->status = ['errored' => $status, 'msg'=>$message];
	  // return $this->
	}

	public function getStatus(){
		// get $status
		return $this->status;
	}

	private function __destruct(){
        parent::close();
    }
}

// $db = new Database();
$db = new Database(['table'=>'logins']);
// print_r($db->insert(
// [
// 	'username' => 'username9',
// 'password' => 'password',
// 'type' => 'admin'
// ]
// ));
// print_r($db->fetch());//all
// print_r($db->fetch('SELECT * FROM {table} LIMIT 1'));
// print_r($db->table('logins')->fetch('SELECT * FROM {table}', 'multiple'));
// print_r($db->update('UPDATE {table} SET username = "tk" where id = 11'));
// print_r($db->delete('DELETE FROM {table} WHERE username = "username9"'));
// print_r($db->rawQuery('SELECT * FROM {table}')['msg']->affected_rows);