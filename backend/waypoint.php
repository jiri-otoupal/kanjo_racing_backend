<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
prepareJsonAPI();


if (($session_id = $_POST["session_id"]) === null) {
    echo json_encode(fail("Not Signed In"));
    return;
}


$user = new User();
$res = $user->auth($session_id);

$response = array();

if ($res) {
    $req_res = $user->getRaces();
    $response["message"] = "Authenticated Successfully";
    $response["status"] = "OK";
    $response["waypoints"] = $req_res;
} else {
    $response = fail();
}
$response["success"] = true;
echo json_encode($response);