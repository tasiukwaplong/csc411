<?php
/**
 * @author tasiukwaplong
 */
require_once('src/Autoloader.php');
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// listens for webhook sent by paystack
require_once('webhook/Paystack/autoload.php');

######################TESTING HERE###########################
$User = new UsersController();
// print_r($User->addUser(
//     ['first_name'=>'Val_first_name',
//     'last_name'=>'Val_last_name',
//     'email'=>'Val_email',
//     'phone'=>'Val_phone',
//     'address'=>'Val_address',
//     'dob'=>'1996-12-01',
//     'password'=>'Val_password'])
// );
// print_r($User->getUsers());
print_r($User->getUser('10'));
######################TESTING END HERE###########################

// handle post and get requests
if ((strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' )){
    $req = 'post-';
    $req .= (isset($_GET['req'])) ? htmlspecialchars($_GET['req']) : null;
    $input = json_decode(@file_get_contents("php://input")); // convert post content to array
}else{
    $req = 'get-';
    $req .= (isset($_GET['req'])) ? htmlspecialchars($_GET['req']) : null;
    $input = $_GET;
}

switch ($req) {
    case 'post-create-admin':
        $Admin = new AdminsController();
        render(200, $Admin->createAdmin([
            'username' => (isset($input->username)) ? $input->username : null,
            'password' => (isset($input->password)) ? $input->password : null
            ]));
        break;
    case 'post-check-token-validity':
        $Admin = new AdminsController();
        render(200, $Admin->checkTokenValidity(isset($input->token) ? $input->token : null));
        break;

    case 'post-admin-login':
        $Admin = new AdminsController();
        render(200, $Admin->login([
            'username' => (isset($input->username)) ? $input->username : null,
            'password' => (isset($input->password)) ? $input->password : null
        ]));
        break;
    case 'get-get-contestants':
        $Contestant = new ContestantsController();
        render(200, $Contestant->get(@$input['token']));
        break;
    case 'get-get-voters':
        $Votes = new VotesController();
        render(200, $Votes->get(@$input['token']));
        break;
    case 'get-get-contest':
        $Contests = new ContestsController();
        render(200, $Contests->get(@$input['token'], @$input['id']));
        break;
    case 'post-create-contest':
        $Contest = new ContestsController();
        render(200, $Contest->createContest(
            (isset($input->token)) ? $input->token : null,
                [
                    'start_time' => (isset($input->start_time)) ? $input->start_time : null,
                    'stop_time' => (isset($input->stop_time)) ? $input->stop_time : null,
                    'contestants' => (isset($input->contestants)) ? $input->contestants : null,
                    'description' => (isset($input->description)) ? $input->description : null,
                    'category' => (isset($input->category)) ? $input->category : null
        ]));
        break;
    case 'post-change-voting-status':
        $Contest = new ContestsController();
        render(200, $Contest->changeVotingStatus(
            (isset($input->token)) ? $input->token : null,
              [
                  'id' => (isset($input->id)) ? $input->id : null,
                  'status' => (isset($input->status)) ? $input->status : null
              ]));
            break;
    // case 'post-send-invoice2':
    //     $Tranx = new TransactionsController();
    //     render(200, $Tranx->sendInvoice(
    //       isset($input->email) ? $input->email : null,
    //       isset($input->amount) ? $input->amount : null
    //     ));
    //     break;
    case 'post-send-invoice':
        $Votes = new VotesController();
        render(200, $Votes->sendInvoiceLink([
            'amount_paid' => isset($input->amount_paid) ? $input->amount_paid : null,
            'email' => isset($input->email) ? $input->email : null,
            'voted_for' => isset($input->voted_for) ? $input->voted_for : null,
            'contest_id' => isset($input->contest_id) ? $input->contest_id : null
        ]));
        break;
    case 'get-check-link':
        $Votes = new VotesController();
        render(200, $Votes->getInvoiceLinkDetails(@$input['invoice_link']));
        break;
    case 'get-user-data':
        $Contestant = new ContestantsController();
        render(200, $Contestant->getDataFromCloud(@$input['invoice']));
        break;
    case 'post-update-advert':
        $Advert = new AdvertsController();
        render(200, $Advert->getAdvertFromCloud(isset($input->token) ? $input->token : null));
        break;
    case 'get-get-advert':
        $Advert = new AdvertsController();
        render(200, $Advert->get());
        break;
    
    // del test only
    // case 'get-trans':
    //     $Tranx = new TransactionsController();
    //     render(200, $Tranx->sendInvoice('tasiukwaplong@gmail.com', 200));
    //     break;
    // del test only
    
    default:
    // print_r(strtotime('2020-08-24 08:17:28 AM'));
    // $expires = date("Y-m-d h:i:sa", strtotime("+1 week"));
    // print_r($expires)
    // die();
        render(400, ['errored'=>true, 'message'=>'use `req` parameter', "input" => $input]);
        break;
}

function render($status = 400, $data = []){
    http_response_code($status);
    return print_r(json_encode($data));
}