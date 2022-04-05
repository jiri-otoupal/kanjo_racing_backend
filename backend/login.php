<?php


require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
prepareJsonAPI();




if (!isset($_POST["email"]) || !isset($_POST["password"])) {
    echo json_encode(fail());
    return;
}

$user = new User();
$res = $user->login($_POST["email"], $_POST["password"]);

$response = array();

if ($res) {
    $response["message"] = "Logged in Successfully";
    $response["status"] = "OK";
} else {
    $response = fail();
}

echo json_encode($response);