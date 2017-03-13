<?php
    session_start();
    include('partial/header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-l-12" style="width:100%">
            <h2>Leaderboard</h2>
            <table style="width:100%">
                <tr>
                    <th>Commander</th>
                    <th>Victory</th>
                    <th>Defeat</th>
                </tr>

                <?php
                $db = database_connect();
                uasort($db, function ($a, $b) {
                 return $b['win'] - $a['win'];
});
                foreach ($db as $user => $info)
                {
                    echo    (
                            "<tr><td><a href='member.php?user=" . $user . "'>" . $user . "</a></td><td>"
                            . $info['win'] . "</td><td>"
                            . $info['loss'] . "</td></tr>"
                            );
                }
                ?>


            </table>
        </div>
        
    </div>
</div>

<?php include('partial/footer.php'); ?>