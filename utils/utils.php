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

function fail()
{
    $response = array();
    $response["message"] = "Login Failed";
    $response["status"] = "FAIL";
    return $response;
}

function prepareJsonAPI(){
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json; charset=utf-8');
}