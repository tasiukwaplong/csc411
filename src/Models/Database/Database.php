<?php
/**
 * @author: @tasiukwaplong
 */

class Database{
	public $status = ['errored'=> false, 'message'=> ''];
	private $table;
	private $customQuery;
	private $result;
	private $db;

	function __construct($options=['port'=>0, 'table'=>'']){
		if(!empty($options['table'])) $this->table = $options['table'];
		if (!$this->envVarIsSet()) {
			$this->setStatus(true, GENREAL_MESSAGES['env_not_set']);
		} else {
		  $this->db = new mysqli(
			  DB_CONFIG['host'], DB_CONFIG['username'],
			  DB_CONFIG['password'],DB_CONFIG['database'], $options['port']
		  );
		  if (mysqli_connect_errno($this->db)) $this->setStatus(true, mysqli_connect_error($this->db));
	   }
	}

	public function table($tableName = null){
		// set table name using ->table('tableName')
		if(is_null($tableName)) $this->setStatus(true, GENREAL_MESSAGES['table_name_not_set']);
		else $this->table = $tableName;
		return $this;
	}

	public function fetch( $selectQuery = null){
	  // handles SELECT statements
	  if(is_null($selectQuery)) $this->customQuery = 'SELECT * FROM '.$this->table;
	  else $this->customQuery = str_replace('{table}', $this->table, $selectQuery);	  
	  return $this->runQuery()->fetchResults(); 
	}

	private function fetchResults(){
		// run select statements
		if ($this->result->num_rows < 1) {
		  $this->setStatus(true, GENREAL_MESSAGES['no_data_yet']);
		}else {
		  ($this->result->num_rows === 1) ? $this->setStatus(false, $this->result->fetch_assoc()) : $this->fetchAsMultiple();
		}
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
	  $result = $this->db->query($this->customQuery);
	  
	  if(!$result) $this->setStatus(true, mysqli_error($this->db));
	  else $this->result = $result;	
	  return $this;
	}

	private function envVarIsSet(){
	  // check to see if all environmental variables are set
	  if(!DB_CONFIG || gettype(DB_CONFIG) !== 'array') return false;
	  $arrayDiff = array_diff(
	  	['host', 'database', 'username', 'password'],
	  	array_keys(DB_CONFIG)
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

	public function setStatus($status = true, $message = 'An unidentified error just occured. ERR_500'){
	  // set $status errored and message if any 
	  if (gettype($status) !== 'boolean') $this->status['errored'] = true;
	  $this->status = ['errored' => $status, 'message'=>$message];
	  // return $this->
	}

	public function getStatus(){
		// get $status
		return $this->status;
	}

	private function __destruct(){
        $this->db->close();
    }
}
