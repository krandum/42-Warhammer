<?php include('head.php'); ?>
<header class="row">
    <div class="col-l-2 col-m-12 col-s-12 logo">
        <a href="index.php"><img src="../sprites/spacestation.png" alt="Awesome Starships Battles II Logo"></a>
    </div>
    <div class="col-l-6 col-m-8 col-s-12 menu">
        <a href="index.php">HOME</a> |
        <a href="leaderboard.php">LEADERBOARD</a> |
        <a href="chat.php">OPEN COMM-LINK</a> |
        <a href="battle.php">BATTLE</a>
    </div>
    <div class="col-l-4 col-m-4 col-s-12 login">

         <?php

                require_once('model/battledb.php');

            if (isset($_SESSION['username']) && !empty($_SESSION['username'])) 
            {
                if (battle_queue_exist($_SESSION['username']))
                {

                    $bq = battle_queue_connect();
                    if (matchFound($_SESSION['username']) || matchMaker($_SESSION['username']))
                    {
                        //match made so do something about it.
                        echo '<a id="startmatch" href="../game.php"><span id="startmatch">Start Match</span></a> | ';
                    }
                    else
                    {
                        echo "<a>";
                        echo "Queue Time: " . gmdate("i:s", time() - $bq[$_SESSION['username']]['time']);
                        echo "</a> | ";
                    }
                }
                echo '<a href="member.php">Commander '.$_SESSION['username'].'</a> | <a href="logout.php">Logout</a>';
            } 
            else 
            {
                echo '<a href="register.php">Register</a> | <a href="login.php">Login</a>';
            }
        ?>
    </div>
</header>