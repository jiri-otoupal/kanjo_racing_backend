<?php
require_once "db/models/User.php";


header("Access-Control-Allow-Origin: *");

$user = new User();

//$user->register("jiri-otoupal@ips-database.eu", "opaka", "medved");
$res = $user->login("jiri-otoupal@ips-database.eu", "medved");

echo "Welcome to Kanjo Backend\n";
echo "$res\n";
echo $_COOKIE["session_id"];
