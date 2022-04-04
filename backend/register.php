<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";

$user = new User();
$res = $user->register($_POST["email"], $_POST["nickname"], $_POST["password"]);
$user->commit();

$response = array();

if ($res) {
    $response["message"] = "Registered Successfully";
    $response["status"] = "OK";
} else {
    $response["message"] = "User Exists";
    $response["status"] = "FAIL";
}

echo json_encode($response);