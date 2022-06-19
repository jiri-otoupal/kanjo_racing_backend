<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/Race.php";
require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";

prepareJsonAPI();


if (($session_id = $_POST["session_id"]) === null) {
    echo json_encode(fail("Not Signed In"));
    return;
}

$user = new User();
$res = $user->auth($session_id);

/**
 * Check for valid response
 */
if ($res) {
    $response = array();
    $race = new Race();
    if (isset($_POST["op"]) && isset($_POST["race_id"]) && $_POST["op"] == "get_waypoints") {
        $res = $race->getWaypoints($_POST["race_id"]);
        if ($res) {
            $response["message"] = "Got Waypoints for race";
            $response["waypoints"] = $res;
            $response["status"] = "OK";
        } else {
            $response["message"] = "Failed to get waypoints of race";
            $response["status"] = "FAIL";
        }
    } else if (isset($_POST["op"]) && isset($_POST["user_id"]) && $_POST["op"] == "get_joined") {
        $res = $race->getJoined($_POST["user_id"]);
        if ($res) {
            $response["message"] = "Got Joined races";
            $response["races"] = $res;
            $response["status"] = "OK";
        } else if (mysqli_error($race->connection)) {
            $response["message"] = "Failed to get joined races" . mysqli_error($race->connection);
            $response["status"] = "FAIL";
        } else {
            $response["message"] = "No joined races";
            $response["status"] = "OK";
        }
    } else if (isset($_POST["op"]) && isset($_POST["race_id"]) && ($operation = $_POST["op"]) != null && ($race_id = $_POST["race_id"]) != null) {
        $race = new Race($race_id);
        $operation = strtolower($operation);

        if ($operation == "join" && isset($_POST["car_id"]) && ($car_id = $_POST["car_id"]) != null) {
            $res = $race->join($user->getId(), $car_id, $race_id);
        } else if ($operation == "leave") {
            $res = $race->leave($user->getId(), $race_id);
        } else {
            $res = false;
            $response["message"] = "Invalid request or missing parameters";
            $response["status"] = "FAIL";
        }

        if ($res) {
            $response["message"] = "Operation " . $operation . " succeeded";
            $response["status"] = "OK";
        } else {
            if ($car_id == null)
                $response["hint"] = "Missing car"; //TODO alert user
            $response["message"] = "Failed " . $race->connection->error;
        }

    } else if (!isset($_POST["race_id"]) && !isset($_POST["waypoints"]) && !isset($_POST["delete"])) {

        $req_res = $race->getAll();
        $response["message"] = "Authenticated Successfully";
        $response["status"] = "OK";
        $response["races"] = $req_res;

    } else if (isset($_POST["race_id"]) && isset($_POST["delete"])) {
        $res = $race->deleteRace($_POST["race_id"], $user->getId());
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
        $start_time = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $_POST["start_time"])));
        $lat = $_POST["latitude"];
        $lng = $_POST["longitude"];

        $min_racers = $_POST["min_racers"];
        $max_racers = $_POST["max_racers"];
        $max_hp = $_POST["max_hp"];
        $pass = $_POST["password"];
        $heat_grade = $_POST["heat_grade"];
        $min_karma = $_POST["min_req_karma"];
        $chat_link = $_POST["chat_link"];
        $laps = $_POST["laps"];

        $owner_id = $user->getId();

        $operation = null;


        $race_data = $race->getRace($race_id);

        if ($race_data == null) {
            $operation = "add";
            $res = $race->add($name, $start_time, $lat, $lng, $owner_id, $min_racers,
                $max_racers, $max_hp, $pass, $heat_grade, $min_karma, $chat_link, $laps, $img_url);
            $response["message"] = "Success Inserted New Race";


        } else if ($race_data["owner_id"] === $user->getId()) {
            $operation = "modify";
            $res = $race->modifyRace($race_id, $name, $start_time, $lat, $lng, $owner_id, $min_racers,
                $max_racers, $max_hp, $pass, $heat_grade, $min_karma, $chat_link, $laps, $img_url);
            $response["message"] = "Success Modified Race";
        }

        if (isset($_POST["waypoints"])) {
            //TODO: clear previous waypoints
            $race_id = mysqli_insert_id($race->connection);
            $waypoints = $_POST["waypoints"];
            foreach ($waypoints as $waypoint) {
                $res = $race->addWaypoint($race_id, $waypoint["step"], $waypoint["lat"], $waypoint["lng"]);
                if (!$res)
                    $response["message"] = "Failed to insert waypoint";
            }

            if ($res) {
                $response["message"] = "Waypoints Inserted";
                $response["status"] = "OK";
                $user->commit();
            } else {
                $response = fail("Failed to insert waypoint");
            }

        }

        if ($res) {
            $response["status"] = "OK";
            $race->commit();
        } else {
            $response = fail("Failed to " . $operation . " race " . $race->connection->error);
        }
    }
} else {
    $response = fail();
}
$response["success"] = true;
echo json_encode($response);