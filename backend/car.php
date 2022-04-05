<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
prepareJsonAPI();
session_start();

$session_id = $_SESSION["session_id"];


if (!isset($session_id)) {
    echo json_encode(fail());
    return;
}

$user = new User();
$res = $user->auth($session_id);

$response = array();

if ($res) {
    $req_res = $user->getCars();
    $response["message"] = "Authenticated Successfully";
    $response["status"] = "OK";
    $response["cars"] = $req_res;
} else {
    $response = fail();
}

echo json_encode($response);