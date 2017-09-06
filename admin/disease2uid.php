<?php
$disease=$_GET["disease"];

require '../redis.php';
use phpish\redis;

$redis = redis\client();

$uid = $redis('smembers diseases_'.$disease);
echo json_encode($uid);
?>

