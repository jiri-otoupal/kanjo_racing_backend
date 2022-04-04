<?php
require "db/models/User.php";

header("Access-Control-Allow-Origin: *");
echo "Welcome to Kanjo Backend";

$user = new User();

echo $user->register("jiri-otoupal@ips-database.eu","opaka","medved");
echo (int)$user->login("jiri-otoupal@ips-database.eu","medved");