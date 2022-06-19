<?php
require_once "db/models/User.php";


header("Access-Control-Allow-Origin: *");

$user = new User();

echo "Welcome to Kanjo Backend\n";
