<?php
    session_start();
	require_once('model/battledb.php');
    battle_queue_del($_SESSION['username']);
    session_destroy();
    header('Location: index.php');
?>