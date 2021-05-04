<?php
class PlansController extends Database{
	public $tableName = 'plans';

	function __construct() {
		// parent::table($this->tableName);
		parent::__construct(['table'=>$this->tableName]);
	}

	public function addPlan($adminToken = null, $data = []){
	  // add plans
	  if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);

		$expected_fields = ['name', 'vehicle_type', 'ncb', 'engine_size', 'year_of_manufacture', 'driving_experince', 'involvement_in_car_accident', 'conviction_of_any_driving_offence', 'price'];

		$keyExist = array_diff($expected_fields, array_keys($data));
		if (count($keyExist) !== 0 || count($data) <= 5) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
		
		$planData = $this->getPlan($data['name'])['message'];
		
		if (!is_null($planData['name'])) return $this->status(true, GENREAL_MESSAGES['plan_exists']);

		if (parent::insert($data)['errored']) return $this->status(true, GENREAL_MESSAGES['plan_not_added'] ?? $this->status['message']);
		else return $this->status(false, GENREAL_MESSAGES['plan_added']);
	}

	private function isAdmin($token){
	  // check if is an admin making request
	  return !is_null($token);
	}

	public function getPlan($column, $getByName = true){
	  // get plan details by plan name
	  $user = ($getByName)
	    ? parent::fetch("SELECT * FROM {table} WHERE name = '$column' LIMIT 1")
	    : parent::fetch("SELECT * FROM {table} WHERE id = '$column' LIMIT 1");
	  return (!$user['errored'] && !is_null($user['message']['name']))
	    ? $this->status(false, $user['message'])
	    : $this->status(false, null);
	}

	public function getPlans(){
	  // fetch all plans
	  return parent::fetch();
	}

	public function deletePlan($adminToken = null, $planName = null){
	  // delete plan by name
	   if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);
	   if (is_null($planName)) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
	   $planData = $this->getPlan($planName)['message'];
	   if (is_null($planData['name'])) return $this->status(true, GENREAL_MESSAGES['plan_not_exists']);
	   if (parent::delete("DELETE FROM {table} WHERE name = '$planName' LIMIT 1")['errored']) return $this->status(true, GENREAL_MESSAGES['plan_not_deleted'] ?? $this->status['message']);
		else return $this->status(false, GENREAL_MESSAGES['plan_deleted']);
	}

	public function searchPlans($data = ''){
		$expected_fields = ['type','engine_size','ncb','year_of_manufacture','driving_experience','involvement_in_any_motor_accident'];

		$keyExist = array_diff($expected_fields, array_keys($data));
		if (count($keyExist) !== 0 || count($data) <= 5) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
		return $this->status(true, GENREAL_MESSAGES['plan_not_found']);
	}

	private function status($status, $message){
		parent::setStatus($status, $message);
		return $this->status;
	}
}