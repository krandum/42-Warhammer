<?php
    session_start();
    require_once('model/people.php');
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }
    $people = people_exist($_SESSION['username']);
    if ($people === null) {
        header('Location: login.php');
        exit();
    }
    include('partial/header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-l-12" style="position: relative; left: 25vw; width:50%">
            <h2>Awesome Starship Battleroom</h2>
            <form action="controller/people.php" method="POST" style="padding: 12px 6px;">
                Factions: 
                <input type="radio" name="faction" value="red" checked> Red
                <input type="radio" name="faction" value="green"> Green
                <input type="radio" name="faction" value="blue"> Blue <br />
                Fleet Strength: 
                <input type="radio" name="fleet" value="500" checked> 500
                <input type="radio" name="fleet" value="1500"> 1500
                <input type="radio" name="fleet" value="3000"> 3000 
                <button type="submit" class="btn btn-default">Search for Enemy Battleships</button>
                <input type="hidden" name="username" value="<?php echo $_SESSION['username']; ?>">
                <input type="hidden" name="opponent" value="-1">
                <input type="hidden" name="from" value="battle">
                <input type="hidden" name="success" value="chat">
            </form>
        </div>
    </div>
</div>
<?php include('partial/footer.php'); ?>