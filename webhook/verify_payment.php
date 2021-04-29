<?php
set_time_limit(180);//3 minutes for waiting

require_once('../src/Autoloader.php');
require_once('Paystack/autoload.php');

@header('Content-Type: application/json');
@header("Access-Control-Allow-Origin: *");
@header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
@header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


define('PAYSTACK_KEY', 'sk_test_5706c144312501134be78eb5dcdf30989c387c03');

// only a post with paystack signature header gets our attention
((strtoupper($_SERVER['REQUEST_METHOD']) !== 'GET' ))
  ? die(http_response_code(400)) : http_response_code(200);

$ref = (isset($_GET['ref'])) ? $_GET['ref'] : null;
$Tranx = new TransactionsController(PAYSTACK_KEY);
$input = $Tranx->fetchTransactionData($ref);
if ($input === false || is_null($input)){
    print_r(json_encode(['errored'=>true,'message'=>'Could  not validate payment']));
    die();
}
$event = ($input === false || is_null($input)) ? null : $input; 


$payment_category = (isset($event->metadata->custom_fields[2]->value))
  ? $event->metadata->custom_fields[2]->value : 'vote';//contest || vote

if($payment_category === 'contest'){
    // if a contestatnt webhook hit
    print_r(registerContestant($event));
}else{
    print_r(registerVoter($event));
}

function registerContestant($event, $isAdmin = false){
    // register contestant
    if (gettype($event) !== 'object')
        return json_encode(['errored'=>true,'message'=>'Unable to verify payment']);
    global $Tranx;
    $Contestant = new ContestantsController();

    $newContestatant = $Contestant->registerContestant([
            'first_name'=>$event->customer->first_name,
            'last_name'=>$event->customer->last_name,
            'email'=>$event->customer->email,
            'phone'=>$event->customer->phone,
            'amount_paid'=>$event->amount,
            'invoice'=>$event->reference,
            'date_paid'=>$event->paid_at,
            'category'=>$event->metadata->custom_fields[0]->value,
            'display_name'=>$event->metadata->custom_fields[1]->value
    ]);
    $newContestatant['message']  = ($isAdmin && $newContestatant['errored'])
      ? $newContestatant['message'] : 'This payment has been verified.';
    return json_encode($newContestatant);
}

function registerVoter($event, $isAdmin = false){
    // make a vote count
    if (gettype($event) !== 'object')
        return json_encode(['errored'=>true,'message'=>'Unable to verify payment']);
    global $Tranx;
    $Votes = new VotesController();

    $newVote = $Votes->registerVote([
            'first_name'=>$event->customer->first_name,
            'last_name'=>$event->customer->last_name,
            'email'=>$event->customer->email,
            'phone'=>$event->customer->phone,
            'amount_paid'=>$event->amount,
            'invoice'=>$event->reference,
            'time_of_vote'=>$event->paid_at,
            'email_contestID'=> 
            $event->customer->email.''.$event->metadata->custom_fields[0]->value,
            'contest_id'=>$event->metadata->custom_fields[0]->value,
            'voted_for'=>$event->metadata->custom_fields[1]->value
        ]);
        $newVote['message'] = ($isAdmin && $newVote['errored'])
          ? $newVote['message']
          : 'Could not verify payment at the moment. This may happen if payment has already been verified (You should a get a mail when that happens) or Keep refreshing this page';
        return json_encode($newVote);
    }
