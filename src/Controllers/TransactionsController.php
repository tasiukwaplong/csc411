<?php

class TransactionsController extends Yabacon\Paystack{
    private $payStackKey = DB_CONFIG['paystack_private_key'] ?? null;
    public $status = ['errored'=>false, "message"=>''];
    public $tableName = 'transaction';

    function __construct($payStackKey = null) {
        $PAYSTACK_KEY = (is_null($payStackKey)) ? $this->payStackKey : $payStackKey; 
        try {
           parent::__construct($PAYSTACK_KEY);
         } catch (Exception $e) {
           $this->status(true, GENREAL_MESSAGES['paystack_key_error']);
         } 
    }

    public function isHavingSuccessEvent($transaction) {
        # check if success event exist
        return ('success' === $transaction->data->status);
    }

    public function fetchTransactionData($ref = null){
      // fetch tranx data
      if (is_null($ref)) return $this->status(true, GENREAL_MESSAGES['payment_not_verified']);

      $tranx = json_decode($this->paystack_http("transaction/verify/$ref", 'GET', null));

      if (!isset($tranx->status) || !isset($tranx->status)) return $this->status(true, GENREAL_MESSAGES['payment_not_verified']);
      // check if transaction is successful or not
      if (!$this->isHavingSuccessEvent($tranx)) return $this->status(true, 'Payment was not successful.');
      // extract to variables
      extract($this->getRequiredData($tranx));
      $DB = new Database();
      $DB->table('transactions');
      // insert to db
      if (!$DB->insert([
             'transaction_id' => $transactions_id,
             'user_id' => $user_id
         ])['errored'])
      {
        //if success, add user_insurance_policy
        $Policy = new UserInsurancePolicies();
        return $Policy->createInsurancePolicy([
          'transactions_id'=>mysqli_insert_id($DB->db),
          'amount_paid'=>$amount_paid,
          'user_id'=>$user_id,
          'quotation_request_id'=>$quotation_request_id,
          'plans_id'=>$plans_id,
          'engine_number'=>$engine_number,
          'chassis_number'=>$chassis_number,
          'vehicle_license_number'=>$vehicle_license_number
        ]);
        // return $this->status(false, mysqli_insert_id($DB->db));
      }else {
        return $this->status(true, GENREAL_MESSAGES['unable_to_add_policy']);
      }

      return $this->status(false, 'hello');
    }


    private function getRequiredData($tranxData){
      // extract required info
      $extractedData = [
        'transactions_id'=>$tranxData->data->reference,
        'amount_paid'=>$tranxData->data->amount,
        'user_id'=>$tranxData->data->metadata->custom_fields[0]->value,
        'quotation_request_id'=>$tranxData->data->metadata->custom_fields[1]->value ?? 'NULL',
        'plans_id'=>$tranxData->data->metadata->custom_fields[2]->value ?? 'NULL',
        'engine_number'=>$tranxData->data->metadata->custom_fields[3]->value,
        'chassis_number'=>$tranxData->data->metadata->custom_fields[4]->value,
        'vehicle_license_number'=>$tranxData->data->metadata->custom_fields[4]->value,
      ];

      return $extractedData;
      // $tranx->data->metadata->custom_fields;
    }



    /*public function referenceIsBeingPaid($refNo){
        # check if payment actually exists
        $refStatus = json_decode($this->paystack_http("transaction/verify/$refNo", 'GET', null));
        return (!is_null($refStatus) && isset($refStatus->status) && ($refStatus->status));
    }*/

    /*public function receiptInformation($transaction, $dataToReturn = null){
        # get information regarding receipt such as payer, amount etc.
        $reference = (isset($transaction->description->data->reference))
          ? $transaction->description->data->reference
          : null;
        if ($this->isHavingSuccessEvent($transaction) && $this->referenceIsBeingPaid($reference)) {
            return $transaction;
        }

        return null; // invalid transaction
    }*/

    /*public function sendInvoice($email, $amount){
      // sendinvoice
      if (is_null($email) || is_null($amount)){
        return [
                'errored'=>true,
                'message'=>'Unable to send invoice. Check if email is valid'
              ];
      }
      //first create a customer
      $customer = json_decode($this->paystack_http("customer", 'POST', ['email'=>$email]));
      $customerID = (isset($customer->data->customer_code)) ? $customer->data->customer_code : null;
      $data = [
        'description' => 'Payment request from Kings and Queens',
        'customer' => $customerID,
        'amount' => $amount,
        'due_date' => '2022-07-09',
        //del test
         'metadata' => [
          'custom_fields' => [
            [
              'display_name' => 'category',
              'variable_name' => 'category',
              'value' => 'voter'
            ],
            [
              'display_name'=> 'Display Name',
              'variable_name'=> 'display_name',
              'value'=> 'Tasiu Display'
            ],
            [
              'display_name'=> 'payment_category',
              'variable_name'=> 'payment_category',
              'value'=> 'vote'
            ]
          ]
        ]
        //del test stop here
      ]; 
      $invoiceStatus = json_decode($this->paystack_http("paymentrequest", 'POST', $data));
      $message = (!is_null($invoiceStatus) && isset($refStatus->status) && ($refStatus->status));
      return ['errored'=>!$message, 'message'=>$message];
    }*/

    private function status($status = true, $message = 'An unidentified error just occured. ERR_500'){
      // set $status errored and message if any 
      if (gettype($status) !== 'boolean') $this->status['errored'] = true;
      $this->status = ['errored' => $status, 'message'=>$message];
      return $this->status;
    }

    private function paystack_http($endpoint, $callMethod, $data){
      //make api call to paystack dashboard
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.paystack.co/$endpoint",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "$callMethod",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
          "authorization: Bearer ".$this->secret_key,
          "content-type: application/json"
        ]
      ]);
      $response = curl_exec($curl);
      $err = curl_error($curl);
      
      curl_close($curl);
      return ($err) ? null : $response;
  
    }
}

