<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
prepareJsonAPI();
session_start();



if (!isset($_SESSION["session_id"])) {
    echo json_encode(fail("Not Signed In"));
    return;
}

$session_id = $_SESSION["session_id"];

$user = new User();
$res = $user->auth($session_id);

$response = array();

if ($res) {
    $req_res = $user->getProfile();
    $response["message"] = "Authenticated Successfully";
    $response["status"] = "OK";
    $response["nickname"] = $req_res["nickname"];
    $response["karma"] = $req_res["karma"];
} else {
    $response = fail();
}

echo json_encode($response);