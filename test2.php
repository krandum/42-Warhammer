<?php

session_start();

$_SESSION['first'] = 0;
$_SESSION['match_id'] = "testMatch";
$_SESSION['fleet_size'] = 500;
$_SESSION['faction'] = "red";
$_SESSION['other_faction'] = "blue";

header("Location: game.php");
exit();

?>