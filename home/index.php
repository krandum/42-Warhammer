<?php
    session_start();
    if (!isset($_SESSION['filepath']))
    	$_SESSION['filepath'] = getcwd();
    include('partial/header.php'); 
?>


<div class="container">
    <div class="row"><h1>Awesome Starships Battles II</h1><img id='indexpic' src="../sprites/index_ship.png" alt="Awesome Starship"></div>
</div>


<?php include('partial/footer.php'); ?>