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
    $req .= (isset($_GET['req'])) ? htmlspecialchars($_GET['req']) : null;
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
    case 'get-user-verify':
        $User = new UsersController();
        render(200, $User->authenticateUser($input['ut']));
        break;  
    default:
        render(400, ['errored'=>true, 'message'=>'use `req` parameter', "input" => $input]);
        break;
}

function render($status = 400, $data = []){
    http_response_code($status);
    return print_r(json_encode($data));
}