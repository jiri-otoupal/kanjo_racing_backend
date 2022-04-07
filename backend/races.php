<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
require_once realpath(dirname(__FILE__) . '/..') . "/db/models/Race.php";
prepareJsonAPI();

if (($session_id = $_POST["session_id"]) === null) {
    echo json_encode(fail("Not Signed In"));
    return;
}


$user = new User();
$res = $user->auth($session_id);

$response = array();

if ($res) {
    $race = new Race();
    $req_res = $race->getAll();
    $response["message"] = "Authenticated Successfully";
    $response["status"] = "OK";
    $response["cars"] = $req_res;
} else {
    $response = fail();
}

echo json_encode($response);