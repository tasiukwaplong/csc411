<?php
/**
 * @author tasiukwaplong
 */

class PaymentController extends Yabacon\Paystack{
    private $payStackKey = DB_CONFIG['paystack_private_key'] ?? null;
    public $status = ['errored'=>false, "message"=>''];

    public function __construct($payStackKey = null) {
        $PAYSTACK_KEY = (is_null($payStackKey)) ? $this->payStackKey : $payStackKey; 
        try {
           parent::__construct($PAYSTACK_KEY);
         } catch (Exception $e) {
           $this->setStatus(true, GENREAL_MESSAGES['paystack_key_error']);
         } 
    }

    function getKey(){
      return DB_CONFIG['paystack_private_key'];
    }

    public function isHavingSuccessEvent($transaction) {
        # check if success event exist
        return ('charge.success' === $transaction->description->event);
    }

    public function fetchTransactionData($ref){
      // fetch tranx data
      $refStatus = json_decode($this->paystack_http("transaction/verify/$ref", 'GET', null));
      return (isset($refStatus->status) && $refStatus->status)
        ? $refStatus->data
        : false;
    }

    public function referenceIsBeingPaid($refNo){
        # check if payment actually exists
        $refStatus = json_decode($this->paystack_http("transaction/verify/$refNo", 'GET', null));
        return (!is_null($refStatus) && isset($refStatus->status) && ($refStatus->status));
    }

    public function receiptInformation($transaction, $dataToReturn = null){
        # get information regarding receipt such as payer, amount etc.
        $reference = (isset($transaction->description->data->reference))
          ? $transaction->description->data->reference
          : null;
        if ($this->isHavingSuccessEvent($transaction) && $this->referenceIsBeingPaid($reference)) {
            return $transaction;
        }

        return null; // invalid transaction
    }

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

    private function setStatus($status = true, $message = 'An unidentified error just occured. ERR_500'){
      // set $status errored and message if any 
      if (gettype($status) !== 'boolean') $this->status['errored'] = true;
      $this->status = ['errored' => $status, 'message'=>$message];
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

