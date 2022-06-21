<?php


require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
prepareJsonAPI();


if (!isset($_POST["email"])) {
    echo json_encode(fail());
    return;
}

$response = array();
$user = new User();
if (isset($_POST["access_token"]) && $_POST["access_token"] != "") {
    $res = $user->FBlogin($_POST);
    $response["auth_method"] = "FB";
} else {
    $res = $user->login($_POST);
    $response["auth_method"] = "normal";
}

/**
 * Check for valid response
 */
if ($res) {
    $response["message"] = "Logged in Successfully";
    $response["status"] = "OK";
    $response["user_id"] = $user->getId();
    $response["cookie"] = $_COOKIE["session_id"];
} else {
    $response = fail();
}
$response["success"] = true;
echo json_encode($response);