<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
prepareJsonAPI();
session_start();


if (($session_id = $_POST["session_id"]) === null) {
    echo json_encode(fail("Not Signed In"));
    return;
}


$user = new User();
$res = $user->auth($session_id);

if ($res) {
    if (!isset($_POST["car_id"]) && !isset($_POST["delete"])) {
        $response = array();

        $req_res = $user->getCars();
        $response["message"] = "Authenticated Successfully";
        $response["status"] = "OK";
        $response["cars"] = $req_res;

    } else if (isset($_POST["delete"])) {
        $res = $user->deleteCar($_POST["car_id"]);
        if ($res) {
            $response["message"] = "Deleted Successfully";
            $response["status"] = "OK";
            $response["cars"] = $user->getCars();
            $user->commit();
        } else {
            $response = fail("Failed to delete car");
        }

    } else {
        $img_url = null;
        if (isset($_POST["img_url_upload"]))
            $img_url = $_POST["img_url_upload"];
        else if (isset($_POST["img_cam"]))
            $img_url = $_POST["img_cam"];

        $car_id = $_POST["car_id"];
        $name = $_POST["name"];
        $brand = $_POST["brand"];
        $hp = $_POST["hp"];
        $vehicle_type = $_POST["vehicle_type"];

        if ($user->getCar($car_id) == null) {
            $res = $user->addCar($name, $brand, $hp, $vehicle_type, $img_url);
            $response["message"] = "Success Inserted New Car";
        } else {
            $res = $user->modifyCar($car_id, $name, $brand, $hp, $vehicle_type, $img_url);
            $response["message"] = "Success Modified Car";

        }
        if ($res) {
            $response["status"] = "OK";
            $user->commit();
        } else {
            $response = fail("Failed to modify/add car");
        }
    }
} else {
    $response = fail();
}
$response["success"] = true;
echo json_encode($response);