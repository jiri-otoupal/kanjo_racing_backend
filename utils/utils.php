<?php
function randomPassword($length)
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function fail($msg="Login Failed")
{
    $response = array();
    $response["message"] = $msg;
    $response["status"] = "FAIL";
    return $response;
}

function prepareJsonAPI(){
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: X-Requested-With, Origin, Content-Type, X-CSRF-Token, Accept');
}