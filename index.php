<?php
require_once('src/Autoloader.php');
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// listens for webhook sent by paystack
require_once('webhook/Paystack/autoload.php');

######################TESTING HERE###########################
// $User = new UsersController();
// print_r($User->addUser(
//     ['first_name'=>'Val_first_name',
//     'last_name'=>'Val_last_name',
//     'email'=>'tasiukwaplong@gmail.com',
//     'phone'=>'Val_phone',
//     'address'=>'Val_address',
//     'dob'=>'1996-12-01',
//     'password'=>'password']
// )
// );
// print_r($User->authenticateUSer('985090310d58cfba5d85c104aff1b2a3b82e80590'));
// print_r($User->login('tasiukwaplong@gmail.com', '123456'));
// print_r($User->changePassword('1388638922eaf979456970524463429e', '12345', '123456'));
// print_r($User->logout('1388638922eaf979456970524463429e'));
// $Trnx = new TransactionsController();
// render(200, $Trnx->fetchTransactionData('T413549544715378'));

// $Plans = new PlansController();
// print_r($Plans->addPlan('SOME_TOKEN', [
//     'name'=>'name6',
//     'vehicle_type'=>'vehicle_type',
//     'ncb'=>'20',
//     'engine_size'=>'engine_size',
//     'year_of_manufacture'=>'2015',
//     'driving_experince'=>'3',
//     'involvement_in_car_accident'=>false,
//     'conviction_of_any_driving_offence'=>true,
//     'price'=>20000
// ]));
// print_r($Plans->getPlan('name70'));
// print_r($Plans->getPlans());
// print_r($Plans->deletePlan('SOME_ADMIN_TOKEN', 'name2'));
// $Quote = new QuotationRequestsController();
// print_r($Quote->addQuotation([
//     'vehicle_type'=>'vehicle_type',
//     'engine_size'=>'engine_size',
//     'ncb'=>'10',
//     'year_of_manufacture'=>'2014',
//     'years_of_driving_experince'=>3,
//     'involement_in_car_accident'=>true,
//     'conviction_of_any_driving_offence'=>true,
//     'name'=>'full name',
//     'email'=>'email@mail.com',
//     'phone'=>'08042424242'
// ]));
// print_r($Quote->getQuotation('1532549316e2935'));
// print_r($Quote->getQuotations('SOME-ADMIN_TOKEN'));
// print_r($Quote->deleteQuotation('SOME_ADMIN_TOKEN', '1532549316e2935'));
// print_r($Quote->deleteQuotation('SOME_ADMIN_TOKEN', '1532549316e2935'));
// print_r($Quote->approveQuotation('SOME_ADMIN_TOKEN', '1547328805001fc8dccd', 222));
// $Policy = new UserInsurancePolicies();
// print_r($Policy->createInsurancePolicy([
//   'transactions_id'=>2,
//   'amount_paid'=>203000,
//   'user_id'=>39,
//   'quotation_request_id'=>NULL,
//   'plans_id'=>NULL,
//   'engine_number'=>'engine_number',
//   'chassis_number'=>'chassis_number',
//   'vehicle_license_number'=>'vehicle_license_number'
// ]));

######################TESTING END HERE###########################

// handle post and get requests

if ((strtoupper($_SERVER['REQUEST_METHOD']) === 'POST' )){
    $req = 'post-';
    $req .= (isset($_GET['req'])) ? htmlspecialchars($_GET['req']) : null;
    $input = json_decode(@file_get_contents("php://input")); // convert post content to array
    $input = 
        (gettype($input) === 'object')
        ? json_decode(@file_get_contents("php://input"), 1)
        : $input;
}else{
    $req = 'get-';
    $req .= (isset($_GET['req'])) ? htmlspecialchars($_GET['req']) : '';
    if (isset($_GET['reference'])) $req .= 'reference';
    $input = $_GET;
}


switch ($req) {
    case 'post-user-create':
        $User = new UsersController();
        render(200, $User->addUser($input));
        break;
    case 'post-user-login':
        $User = new UsersController();
        render(200, $User->login($input['email'], $input['password']));
        break;
    case 'post-user-password':
        $User = new UsersController();
        render(200, $User->changePassword($input['token'], $input['old'], $input['new']));
        break;
    case 'post-user-logout':
        $User = new UsersController();
        render(200, $User->logout($input['token']));
        break;  
    case 'post-plan-create':
       $Plans = new PlansController();
       render(200, $Plans->addPlan($input['token'], $input['data']));
       break;
    case 'post-quote-create':
       $Quote = new QuotationRequestsController();
       render(200, $Quote->addQuotation($input['token'], $input['data']));
       break;
    case 'post-plan-delete':
       $Plans = new PlansController();
       render(200, $Plans->deletePlan($input['token'], $input['name']));
       break;
    case 'post-quotes':
        $Quote = new QuotationRequestsController();
        render(200, $Quote->getQuotations($input['token']));
        break;
    case 'post-quote-delete':
        $Quote = new QuotationRequestsController();
        render(200, $Quote->deleteQuotation($input['token'], $input['ref']));
        break;
    case 'post-quote-approve':
        $Quote = new QuotationRequestsController();
        render(200, $Quote->approveQuotation($input['token'], $input['ref'], $input['price']));
        break;
    case 'get-user-verify':
        $User = new UsersController();
        render(200, $User->authenticateUser($input['ut']));
        break;
    case 'get-plan':
       $Plans = new PlansController();
       render(200, $Plans->getPlan($input['name']));
       break;
    case 'get-plans':
       $Plans = new PlansController();
       render(200, $Plans->getPlans());
       break;
    case 'get-quote':
        $Quote = new QuotationRequestsController();
        render(200, $Quote->getQuotation($input['ref']));
        break;
    case 'get-reference':
         $Tranx = new TransactionsController();
         render(200, $Tranx->fetchTransactionData($input['reference']));
        break;
    case 'post-users-create':
        $Users = new UsersController();
        render(200, $Users->bulkCreate($input['token'], $input['data']));
        break;
    case 'post-plan-search':
        $Plans = new PlansController();
        render(200, $Plans->searchPlans($input));
        break;
    default:
        render(400, ['errored'=>true, 'message'=>'use `req` parameter', "input" => $input]);
        break;
}

function render($status = 400, $data = []){
    http_response_code($status);
    return print_r(json_encode($data));
}