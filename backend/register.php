<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
prepareJsonAPI();

$user = new User();
$res = $user->register($_POST["email"], $_POST["nickname"], $_POST["password"]);
$user->commit();

$response = array();
/**
 * Check for valid response
 */
if ($res) {
    $response["message"] = "Registered Successfully";
    $response["status"] = "OK";
    mail($_POST["email"], "Thanks for registering to Kanjo", "Thanks for registering to kanjo, your account is active now");
} else {
    $response["message"] = "User Exists";
    $response["status"] = "FAIL";
}
$response["success"] = true;
echo json_encode($response);