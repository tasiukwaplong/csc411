<?php

class UsersController extends Database{
	public $tableName = 'users';

	function __construct() {
		// parent::table($this->tableName);
		parent::__construct(['table'=>$this->tableName]);
	}

	public function addUser($data = []){
	  // add users to the db
		$expected_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'dob', 'password'];
		$keyExist = array_diff($expected_fields, array_keys($data));
		if (count($keyExist) !== 0 || count($data) <= 5) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
		$generatePassword = $data['password'];//'@'.strtoupper(substr(md5(mt_rand()), 0, 7)); 
		$generatedToken = mt_rand().substr(md5(mt_rand()), 0, 50);// generate random password
		$data['password'] = password_hash($generatePassword, PASSWORD_DEFAULT); // encrypt the password
		$data['user_token'] = $generatedToken;
		$data['temp_email'] = $data['email'] ;
		unset($data['email']);// delete email key and value
		
		$userType = $this->getUserType($data['temp_email']);
		if (!is_null($userType['active']) && $userType['active'] === '1') return $this->status(true, GENREAL_MESSAGES['account_exists']);

		if (parent::insert($data)['errored']){
			return $this->status(true, GENREAL_MESSAGES['user_add_error'] ?? $this->status['message']);
		}else{
			$link = DB_CONFIG['backend_url'].'?req=user-verify&ut='.$generatedToken;
			$email = new EmailController();
			$email->sendMail(
				$data['temp_email'],
				'[ACTION REQUIRED] New account created',
				"Hello ".$data['last_name'].". <br>Welcome to ABC Transport insurance. You just created an account with us. Click on this link to complete your registration. $link"
			);
			return $this->status(false, GENREAL_MESSAGES['user_add_success']);
		}
	}

	public function login($email = null, $password = null){
	  // login and fetch user_token
	  if(is_null($email) || is_null($password)) return $this->status(true, GENREAL_MESSAGES['email_or_password_empty']);
	  $userType = $this->getUserType($email);

	  if (!is_null($userType['active']) && password_verify($password, $userType['password'])) {
	  	if($userType['active'] === '0') return $this->status(true, GENREAL_MESSAGES['user_not_auth']);
	  	return $this->status(false, $userType['user_token']);
	  }else{
	  	return $this->status(true, GENREAL_MESSAGES['login_incorrect']);
	  }

	}

	private function verifyUser($userToken = null, $email = null){
	 // change active to 1
	 if (is_null($userToken) || is_null($email)) return; // silently exit function
	  parent::update("UPDATE {table} SET active = '1' WHERE user_token = '$userToken'");
	  $this->status(false, 'NULL_ERROR');
	  parent::update("UPDATE {table} SET email = '$email', temp_email = '' WHERE user_token = '$userToken'");
	  $this->status(false, 'NULL_ERROR');
	  parent::delete("DELETE FROM {table} WHERE temp_email = '$email' AND user_token <> '$userToken'");
	}

	public function bulkCreate($adminToken = null, $data = []){
	  if (is_null($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);
	  return $this->status(false, GENREAL_MESSAGES['bulk_succeess']);
	}

	private function getUserType($email){
	  // get type of user. Active, inactive, not exist
	  $user = parent::fetch("SELECT * FROM {table} WHERE temp_email = '$email' OR email = '$email' ORDER BY id DESC LIMIT 1");
	  $this->status(false, '');//unset error message
	  return (!$user['errored'] && !is_null($user['message']['active'])) ? $user['message'] : null;
	}

	public function getUsers($data = []){
	  // fetch users
	  // @TODO: fetch users based on params
		return parent::fetch();
	}

	public function getUser($id_or_email_or_token, $method= 'BY_USER_ID'){
	  // fetch user details based on BY_USER_ID or BY_USER_TOKEN 
	  $id_or_email_or_token = htmlspecialchars($id_or_email_or_token);
	  return ($method === 'BY_USER_ID') 
	    ? parent::fetch("SELECT * FROM {table} WHERE user_token = '$id_or_email_or_token'")
	    : parent::fetch("SELECT * FROM {table} WHERE id = '$id_or_email_or_token' OR email = '$id_or_email_or_token'");
	}

	public function changePassword($userToken, $old = null, $new = null){
	  // change passord for user
	  $user = $this->getUser($userToken);
	  if(is_null($old) || is_null($new)) return $this->status(true, GENREAL_MESSAGES['format_not_correct']);
	  if ($user['errored'] || $user['message']['active'] !== '1') return $this->status(true, GENREAL_MESSAGES['password_change_not_possible']);
	  if (!password_verify($old, $user['message']['password'])) return $this->status(true, GENREAL_MESSAGES['password_mismatch']);
	  $hashedPassword = password_hash($new, PASSWORD_DEFAULT);
	  parent::update("UPDATE {table} SET password = '$hashedPassword' WHERE user_token = '$userToken' LIMIT 1");
	  return $this->status(false, GENREAL_MESSAGES['password_changed']);
	}

	private function changeToken($userToken = ''){
	  // generate another user token
	  $generatedToken = mt_rand().substr(md5(mt_rand()), 0, 22);// generate random password
	  parent::update("UPDATE {table} SET user_token = '$generatedToken' WHERE user_token = '$userToken' LIMIT 1");
	}

	public function authenticateUser($userToken = null){
	  // generate another user token
	  $user = $this->getUser($userToken);
	  if (is_null($userToken) || $user['errored'] || is_null($user['message']['active'])) return $this->status(true, GENREAL_MESSAGES['inavalid_auth']);
	  if ($user['message'] && $user['message']['active'] === '1') return $this->status(true, GENREAL_MESSAGES['already_verified']);
	  $this->verifyUser($userToken, $user['message']['temp_email']);
	  $this->changeToken($userToken);
	  return $this->status(false, GENREAL_MESSAGES['auth_success']);
	}

	public function logout($userToken = null){
	  // log user out of every system
	  if (is_null($userToken)) return $this->status(true, GENREAL_MESSAGES['operation_not_success']);
	  $this->changeToken($userToken);
	  return $this->status(false, GENREAL_MESSAGES['user_logged_out']);
	}
	
	private function status($status, $message){
		parent::setStatus($status, $message);
		return $this->status;
	}
}