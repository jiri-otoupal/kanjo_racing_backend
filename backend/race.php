<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/Race.php";
require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";

prepareJsonAPI();


if (($session_id = $_POST["session_id"]) === null) {
    echo json_encode(fail("Not Signed In"));
    return;
}

function checkIfEmpty($var){
    if(empty($var))
        return null;
    return $var;
}

$user = new User();
$res = $user->auth($session_id);


$race = new Race();



if ($res) {
    if (!isset($_POST["race_id"]) && !isset($_POST["waypoints"])&& !isset($_POST["delete"])) {
        $response = array();
        $req_res = $race->getAll();
        $response["message"] = "Authenticated Successfully";
        $response["status"] = "OK";
        $response["races"] = $req_res;

    } else if (isset($_POST["waypoints"])) {
        $waypoints = $_POST["waypoints"];
        foreach ($waypoints as $waypoint) {
            $res = $race->addWaypoint($_POST["race_id"], $waypoint["step"], $waypoint["lat"], $waypoint["lng"]);
            if (!$res)
                $response["message"] = "Failed to insert waypoint";
        }

        if ($res) {
            $response["message"] = "Waypoints Inserted";
            $response["status"] = "OK";
            $user->commit();
        } else {
            $response = fail("Failed to delete car");
        }

    } else if (isset($_POST["race_id"]) && isset($_POST["delete"])) {
        $res = $race->deleteRace($_POST["race_id"]);
        if ($res) {
            $response["message"] = "Deleted Successfully";
            $response["status"] = "OK";
            $response["races"] = $race->getAll();
            $user->commit();
        } else {
            $response = fail("Failed to delete race");
        }
    } else {
        //UPDATE Race
        $img_url = null;
        if (isset($_POST["img_cam"]))
            $img_url = $_POST["img_cam"];
        $race_id = $_POST["race_id"];
        $name = $_POST["name"];
        $start_time = date('Y-m-d', strtotime(str_replace('-', '/', $_POST["start_time"])));
        $lat = $_POST["latitude"];
        $lng = $_POST["longitude"];

        $min_racers = $_POST["min_racers"];
        $max_racers = $_POST["max_racers"];
        $max_hp =$_POST["max_hp"];
        $pass = $_POST["password"];
        $heat_grade = $_POST["heat_grade"];
        $min_karma = $_POST["min_req_karma"];
        $chat_link = $_POST["chat_link"];
        $owner_id = $user->getId();

        $operation = null;

        if ($race->getRace($race_id) == null) {
            $operation="add";
            $res = $race->add($race_id, $name, $start_time, $lat, $lng, $owner_id, $min_racers,
                $max_racers, $max_hp, $pass, $heat_grade, $min_karma, $chat_link, $img_url);
            $response["message"] = "Success Inserted New Race";

        } else {
            $operation = "modify";
            $res = $race->modifyRace($race_id, $name, $start_time, $lat, $lng, $owner_id, $min_racers,
                $max_racers, $max_hp, $pass, $heat_grade, $min_karma, $chat_link, $img_url);
            $response["message"] = "Success Modified Race";
        }

        if ($res) {
            $response["status"] = "OK";
            $race->commit();
        } else {
            $response = fail("Failed to ".$operation." race ".$race->connection->error);
        }
    }
} else {
    $response = fail();
}
$response["success"] = true;
echo json_encode($response);