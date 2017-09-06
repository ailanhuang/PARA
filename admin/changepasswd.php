<?php

$username=$_GET["username"];
$password=$_GET["password"];

require '../Client.php';
$redis = new Credis_Client('localhost');

$redis->hset("passwords",$username,$password);

echo "password changed";
?>
