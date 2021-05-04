<?php

class QuotationRequestsController extends Database{
	public $tableName = 'quotation_request';

	function __construct() {
		parent::__construct(['table'=>$this->tableName]);
	}

	public function addQuotation($data = []){
	  // add quotation
		$expected_fields = ['vehicle_type','engine_size','ncb','year_of_manufacture','years_of_driving_experince','involement_in_car_accident','conviction_of_any_driving_offence','name','email','phone'];

		$keyExist = array_diff($expected_fields, array_keys($data));
		if (count($keyExist) !== 0 || count($data) <= 5) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
		$data['reference_id'] = mt_rand().substr(md5(mt_rand()), 0, 5);
				
		if (parent::insert($data)['errored']) return $this->status['message'];
		else return $this->status(false, GENREAL_MESSAGES['quotation_added'].' Your reference ID is: '.$data['reference_id']);
	}

	private function isAdmin($token){
	  // check if is an admin making request
	  return !is_null($token);
	}

	public function getQuotation($referenceId){
	  // get quotation details by referenceId
	  $user = parent::fetch("SELECT * FROM {table} WHERE reference_id = '$referenceId' LIMIT 1");
	  return (!$user['errored'] && !is_null($user['message']['name']))
	    ? $this->status(false, $user['message'])
	    : $this->status(false, null);
	}

	public function getQuotationByEmail($email){
	  // get quotation details by email
	  $user = parent::fetch("SELECT * FROM {table} WHERE email = '$email' ORDER BY approved DESC");
	  return (!$user['errored'] && !is_null($user['message']['name']))
	    ? $this->status(false, $user['message'])
	    : $this->status(false, null);
	}

	public function getQuotations($adminToken = null){
	  // fetch all quotations
	  if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);

	  return parent::fetch();
	}

	public function deleteQuotation($adminToken = null, $referenceId = null){
	  // delete quotation by reference ID
	  if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);
	  if (is_null($referenceId)) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
	   $quoteData = $this->getQuotation($referenceId)['message'];
	   if (is_null($quoteData['name'])) return $this->status(true, GENREAL_MESSAGES['quote_not_exists']);
	   if (parent::delete("DELETE FROM {table} WHERE reference_id = '$referenceId' LIMIT 1")['errored']) return $this->status(true, GENREAL_MESSAGES['quote_not_deleted'] ?? $this->status['message']);
		else return $this->status(false, GENREAL_MESSAGES['quote_deleted']);
	}

	public function approveQuotation($adminToken = null, $referenceId = null, $price = 0){
	  // approve quotation request by ADMIN
	  if (!$this->isAdmin($adminToken)) return $this->status(true, GENREAL_MESSAGES['admin_not_auth']);
	  if (is_null($referenceId) || $price <= 0) return $this->status(true, GENREAL_MESSAGES['insert_input_incomplete']);
	   $quoteData = $this->getQuotation($referenceId)['message'];
	   if (is_null($quoteData['name'])) return $this->status(true, GENREAL_MESSAGES['quote_not_exists']);
	   
	   if(parent::update("UPDATE {table} SET approved = '1', price = $price WHERE reference_id = '$referenceId'")['errored']){
	   
	    return $this->status(true, GENREAL_MESSAGES['quote_not_updated'] ?? $this->status['message']);	  	
	   }else{
	     $email = new EmailController();
   		 $email->sendMail(
		 $quoteData['email'],
		  'YOUR QUOTATION IS READY '.$quoteData['reference_id'],
		  "Hello ".$quoteData['name'].". <br>Your quotation with reference id: ".$quoteData['reference_id']." has been approved. quotation price approved is: ".$price.". An invoice will be sent to you shortly"
		);
	    return $this->status(false, GENREAL_MESSAGES['quote_updated'].'. Reference id: '.$quoteData['reference_id']);
	  }

	}

	private function status($status, $message){
		parent::setStatus($status, $message);
		return $this->status;
	}
}