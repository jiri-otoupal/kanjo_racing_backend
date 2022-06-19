<?php
/**
 * @param $length
 * @return string
 */
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

/**
 * @param $lat1
 * @param $lng1
 * @param $lat2
 * @param $lng2
 * @param $radius
 * @return float|int
 */
function gps2m($lat1, $lng1, $lat2, $lng2, $radius = 6378137)
{
    static $x = M_PI / 180;
    $lat1 *= $x; $lng1 *= $x;
    $lat2 *= $x; $lng2 *= $x;
    $distance = 2 * asin(sqrt(pow(sin(($lat1 - $lat2) / 2), 2) + cos($lat1) * cos($lat2) * pow(sin(($lng1 - $lng2) / 2), 2)));

    return $distance * $radius;
}

/**
 * @param $msg
 * @return array
 */
function fail($msg = "Login Failed")
{
    $response = array();
    $response["message"] = $msg;
    $response["status"] = "FAIL";
    return $response;
}

/**
 * @return void
 */
function prepareJsonAPI()
{
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: X-Requested-With, Origin, Content-Type, X-CSRF-Token, Accept');
}