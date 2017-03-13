<?php

$_SESSION['first'] = 1;
$_SESSION['match_id'] = "testMatch";
$_SESSION['fleet_size'] = 500;
$_SESSION['faction'] = "blue";
$_SESSION['other_faction'] = "red";

header("Location: game.php");
?>