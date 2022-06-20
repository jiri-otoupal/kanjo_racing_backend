<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";
require_once realpath(dirname(__FILE__) . '/..') . "/db/models/Race.php";
prepareJsonAPI();


if (($session_id = $_POST["session_id"]) === null) {
    echo json_encode(fail("Not Signed In"));
    return;
}


$user = new User();
$res = $user->auth($session_id);

$response = array();
/**
 * Check for valid response
 */
if ($res && isset($_POST["race_id"]) && isset($_POST["latitude"]) && isset($_POST["longitude"])) {
    $race_id = $_POST["race_id"];
    $lat = $_POST["latitude"];
    $lng = $_POST["longitude"];

    $req_res = $user->addLocation($lat, $lng);

    $next_waypoint = $user->getNextWaypoint($race_id);
    $dtw = gps2m($lat, $lng, $next_waypoint["latitude"], $next_waypoint["longitude"]);

    /**
     * Collect Waypoints on radius intersect
     */
    if ($dtw <= METERS_TO_WAYPOINT) {
        $race = new Race();
        $user_id = $user->getId();

        $steps_max = $race->getRaceSteps($race_id);
        $user_info = $race->getUserRaceInfo($user_id, $race_id);

        if ($steps_max == $user_info["step"])
            $race->nextLap($user_id, $race_id);
        else
            $race->nextWaypoint($user_id, $race_id);

        $response["collect"] = true;
    }

    $response["step"] = $next_waypoint["step"];
    $response["lat"] = $next_waypoint["latitude"];
    $response["lng"] = $next_waypoint["longitude"];
    $response["dtw"] = $dtw;
    $response["success"] = true;
    $response["racers"] = $user->getUserLocations($race_id);
} else {
    $response["success"] = false;
    $response = fail();
}

echo json_encode($response);