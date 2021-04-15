<?php
// add new advert or contestant via webhook
require_once('../../src/Autoloader.php');

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// only a post with paystack signature header gets our attention
((strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST' ))
  ? die(http_response_code(400)) : http_response_code(200);

// Retrieve the request's body
$input = @file_get_contents("php://input");
$data = json_decode($input);
// if this call does not contain contestant or advert
switch ($data->type) {
    case 'contestant':
        registerContestant($data);
        break;
    case 'advert':
        registerAdvert($data);
        break;
    
    default:
        die(http_response_code(400));
        break;
}

function registerAdvert($advert){
    // add new advert
    $Advert = new AdvertsController();
    if (!isset($advert->advert_code)) die(json_encode(['errored'=>true,'message'=>'Incomplete or invalid input']));
    print_r(json_encode($Advert->upsertAdvert([$advert])));// no use for this: server-to-server call
}

function registerContestant($contestant){
    // update contestant record
    $Contestant = new ContestantsController();
    print_r(json_encode($Contestant->updateContestantData($contestant)));// no use for this
}