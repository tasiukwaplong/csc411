<?php

class UserInsurancePolicies extends Database{
	public $tableName = 'user_insurance_policies';

	function __construct() {
		parent::__construct(['table'=>$this->tableName]);
	}

	public function createInsurancePolicy($data = []){
	  // add user insurance policy
      $expected_fields = ['transactions_id', 'amount_paid', 'user_id', 'plans_id', 'quotation_request_id', 'engine_number', 'chassis_number', 'vehicle_license_number'];
		
		$keyExist = array_diff($expected_fields, array_keys($data));
		if (count($keyExist) !== 0 || count($data) <= 5) return $this->status(true, GENREAL_MESSAGES['unable_to_add_policy']);

		$endDate = date('Y-m-d H:i:s', strtotime("+ 1 year"));

		$dataToInsert = [
			'start_date'=>date('Y-m-d H:i:s'),
			'end_date'=>date('Y-m-d H:i:s', strtotime("+ 1 year")),
			'amount_paid'=>$data['amount_paid'],
			'engine_number'=>$data['engine_number'],
			'chassis_number'=>$data['chassis_number'],
			'vehicle_license_number'=>$data['vehicle_license_number'],
			'user_id'=>$data['user_id'],
			'plans_id'=>$data['plans_id'] ?? 'NULL',
			'quotation_request_id'=>$data['quotation_request_id'] ?? 'NULL',
			'transactions_id'=>$data['transactions_id']
	  	  ];
	  	  extract($dataToInsert);
	  	  $query = "INSERT INTO {table} (start_date,end_date,amount_paid,engine_number,chassis_number,vehicle_license_number,user_id,plans_id,quotation_request_id,transactions_id) VALUES('$start_date','$end_date','$amount_paid','$engine_number','$chassis_number','$vehicle_license_number',$user_id,$plans_id,$quotation_request_id,$transactions_id)";

	  	  if (parent::rawQuery($query)['errored']) return $this->status(true, GENREAL_MESSAGES['unable_to_add_policy']);
	      else return $this->status(false, GENREAL_MESSAGES['policy_added'].'Your insurance cover will last for 1 year ('.$dataToInsert['start_date'].'-'.$dataToInsert['end_date'].').');
	}

	private function isAdmin($token){
	  // check if is an admin making request
	  return !is_null($token);
	}

	public function getPolicy($id){
	  // get quotation details by referenceId
	  $policy = parent::fetch("SELECT * FROM {table} WHERE id = '$id' LIMIT 1");
	  return (!$policy['errored'] && !is_null($policy['message']['name']))
	    ? $this->status(false, $policy['message'])
	    : $this->status(false, null);
	}

	public function getPolicies($adminToken = null){
	  // fetch all quotations
	  if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);

	  return parent::fetch();
	}

	public function deletePolicy($adminToken = null, $id = null){
	  // delete quotation by reference ID
	  if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);
	  if (is_null($id)) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
	   $quoteData = $this->getQuotation($id)['message'];
	   if (is_null($quoteData['name'])) return $this->status(true, GENREAL_MESSAGES['quote_not_exists']);
	   if (parent::delete("DELETE FROM {table} WHERE reference_id = '$id' LIMIT 1")['errored']) return $this->status(true, GENREAL_MESSAGES['quote_not_deleted'] ?? $this->status['message']);
		else return $this->status(false, GENREAL_MESSAGES['quote_deleted']);
	}

	public function approveQuotation($adminToken = null, $id = null, $price = 0){
	  // approve quotation request by ADMIN
	  if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);
	  if (is_null($id) || $price <= 0) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
	   $quoteData = $this->getQuotation($id)['message'];
	   if (is_null($quoteData['name'])) return $this->status(true, GENREAL_MESSAGES['quote_not_exists']);
	   
	   if(parent::update("UPDATE {table} SET approved = '1', price = $price WHERE reference_id = '$id'")['errored']){
	   
	    return $this->status(true, GENREAL_MESSAGES['quote_not_updated'] ?? $this->status['message']);	  	
	   }else{
	     $email = new EmailController();
   		 $email->sendMail(
		 $quoteData['email'],
		  'YOUR QUOTATION IS READY '.$quoteData['reference_id'],
		  "Hello ".$quoteData['name'].". <br>Your quotation with reference id: ".$quoteData['reference_id']." has been approved. Kindly proceed to make payment of HKD".$price
		);
	    return $this->status(false, GENREAL_MESSAGES['quote_updated'].'. Reference id: '.$quoteData['reference_id']);
	  }

	}

	private function status($status, $message){
		parent::setStatus($status, $message);
		return $this->status;
	}
}