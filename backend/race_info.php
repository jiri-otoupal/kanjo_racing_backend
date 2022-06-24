<?php

require_once realpath(dirname(__FILE__) . '/..') . "/db/models/Race.php";
require_once realpath(dirname(__FILE__) . '/..') . "/db/models/User.php";


/**
 * Check for valid response
 */

$db = new DB();
$time_zone = "-02:00";
$previous_races = $db->query("select race.name as rname,user_race_fk.pass_time, u.nickname, c.name, c.vehicle_type, c.brand
                                                from race
                                                         JOIN user_race_fk on race.race_id = user_race_fk.race_id
                                                         JOIN user u on user_race_fk.user_id = u.user_id
                                                         JOIN car c on c.id = user_race_fk.car_id
                                                where start_time <= NOW() ORDER BY race.race_id;");
$future_races = $db->query("select race.name as rname,user_race_fk.pass_time, u.nickname, c.name, c.vehicle_type, c.brand
                                                from race
                                                         JOIN user_race_fk on race.race_id = user_race_fk.race_id
                                                         JOIN user u on user_race_fk.user_id = u.user_id
                                                         JOIN car c on c.id = user_race_fk.car_id
                                                where start_time >=NOW() ORDER BY race.race_id;");
function output_info($races, $future = false)
{

    $lname = "";
    foreach ($races as $race) {
        if ($lname != $race["rname"]) {
            $lname = $race["rname"];
            echo "<h3>" . $race["rname"] . "</h3><br>";
        }
        $str = "Racer <b>" . $race["nickname"] . "</b> Car " . $race["name"] . " Vehicle Type " . $race["vehicle_type"] . " Brand " . $race["brand"];
        if (!$future) {
            $c_str = $str . " Elapsed Time " . $race["pass_time"] . "<br>";
            echo $c_str;
        } else {
            echo $str . "<br>";
        }
    }
}

echo "<h1>Previous Races</h1>";
output_info($previous_races);

echo "<h1>Future Races</h1>";
output_info($future_races, true);