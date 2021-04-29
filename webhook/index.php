<?php
require_once('../src/Autoloader.php');
require_once('Paystack/autoload.php');

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


// define('PAYSTACK_KEY', 'sk_test_5706c144312501134be78eb5dcdf30989c387c03');
define('PAYSTACK_KEY', 'sk_live_e20ed6aa097e165a297ad09207e30896597c24e8');

// only a post with paystack signature header gets our attention
//paystack starts here
// if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('x-paystack-signature', $_SERVER) )    
// exit();
// Retrieve the request's body
// $input = @file_get_contents("php://input");
// define('PAYSTACK_SECRET_KEY','sk_test_5706c144312501134be78eb5dcdf30989c387c03');
// validate event do all at once to avoid timing attack
// if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY))
    // exit();
// http_response_code(200);
// parse event (which is json string) as object
// Do something - that will not take long - with $event
// $event = json_decode($input);
//paystack ends here


// only a post with paystack signature header gets our attention
((strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ))
  ? die(http_response_code(400)) : http_response_code(200);

// Retrieve the request's body
$input = @file_get_contents("php://input");
// $input = $d;
$event = json_decode($input);
$Tranx = new TransactionsController(PAYSTACK_KEY);
$payment_category = (isset($event->description->data->metadata->custom_fields[2]->value))
  ? $event->description->data->metadata->custom_fields[2]->value : 'vote';//contest || vote
  // $i = new TransactionsController(PAYSTACK_KEY);
  // print_r(json_encode($i->sendInvoice($event->description->data->customer->email, 2000)));
  // exit();
if($payment_category === 'contest'){
    // if a contestatnt webhook hit
    print_r(registerContestant($event));
}else{
    print_r(registerVoter($event));
}

function registerContestant($event){
    // register contestant
    global $Tranx;
    $Contestant = new ContestantsController();
    if(!is_null($Tranx->receiptInformation($event))){
        // if payment is valid
        $newContestatant = $Contestant->registerContestant([
            'first_name'=>$event->description->data->customer->first_name,
            'last_name'=>$event->description->data->customer->last_name,
            'email'=>$event->description->data->customer->email,
            'phone'=>$event->description->data->customer->phone,
            'amount_paid'=>$event->amount,
            'invoice'=>$event->description->data->reference,
            'date_paid'=>$event->description->data->paid_at,
            'category'=>$event->description->data->metadata->custom_fields[0]->value,
            'display_name'=>$event->description->data->metadata->custom_fields[1]->value
        ]);
        return json_encode($newContestatant);
    }
    exit();
}

function registerVoter($event){
    // make a vote count
    global $Tranx;
    $Votes = new VotesController();

    if(is_null($Tranx->receiptInformation($event))){
        // if payment is valid
        $newVote = $Votes->registerVote([
            'first_name'=>$event->description->data->customer->first_name,
            'last_name'=>$event->description->data->customer->last_name,
            'email'=>$event->description->data->customer->email,
            'phone'=>$event->description->data->customer->phone,
            'amount_paid'=>$event->amount,
            'invoice'=>$event->description->data->reference,
            'time_of_vote'=>$event->description->data->paid_at,
            'email_contestID'=> 
            $event->description->data->customer->email.''.$event->description->data->metadata->custom_fields[0]->value,
            'contest_id'=>$event->description->data->metadata->custom_fields[0]->value,
            'voted_for'=>$event->description->data->metadata->custom_fields[1]->value
        ]);
        print_r(json_encode($newVote)); // not of use
    }
    exit();
}
