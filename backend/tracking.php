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
    $req_res = $user->addLocation($_POST["latitude"], $_POST["longitude"]);
    $response["success"] = true;
    $response["racers"] = $user->getUserLocations();
} else {
    $response["success"] = false;
    $response = fail();
}

echo json_encode($response);