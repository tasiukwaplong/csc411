<?php
/**
 * @author: tasiukwaplong
 */
class UsersController extends Database{
	public $tableName = 'user';

	function __construct() {
		// parent::table($this->tableName);
		parent::__construct(['table'=>$this->tableName]);
	}

	public function addUser($data = []){
	  // add users to the db
	  $expected_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'dob', 'password'];
	  $keyExist = array_diff($expected_fields, array_keys($data));
	  if (count($keyExist) !== 0) {
	  	// parent::setStatus(true, GENREAL_MESSAGES['insert_input_incomplete']);
	  	return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
	  }
	  $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
	  if (parent::insert($data)['errored']){
	    return $this->status(true, GENREAL_MESSAGES['user_add_error'].$this->status['message']);
	  }else{
	  	/* @TODO: sendEmail to user */
	  	return $this->status(true, GENREAL_MESSAGES['user_add_success']);
	  }
	}

	public function getUsers($data = []){
	  // fetch users
	  // @TODO: fetch users based on params
	  return parent::fetch();
	}
	
	public function getUser($id_or_email){
	  // fetch user details based on email or id
	  $id_or_email = htmlspecialchars($id_or_email);
	  return parent::fetch("SELECT * FROM {table} WHERE id = '$id_or_email' OR email = '$id_or_email'");
	}
	
	private function status($status, $message){
	  parent::setStatus($status, $message);
	  return $this->status;
	}
}