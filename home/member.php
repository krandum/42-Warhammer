<?php
    session_start();
    require_once('model/people.php');
    if ($_GET['user'])
    {
        $user = $_GET['user'];
        $_GET['user'] = '';
    }
    else
        $user =($_SESSION['username']);
    if (!isset($user) || empty($user)) {
        header('Location: index.php');
        exit();
    }
    $people = people_exist($user);
    if ($people === null) {
        header('Location: index.php');
        exit();
    }
    include('partial/header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-l-6">
            <h2>Records</h2>
            <table>
                <tr>
                    <th>Commander</th>
                    <th>Victory</th>
                    <th>Defeat</th>
                </tr>
                <?php
                $db = database_connect();
                echo ("<tr><td>" . $user . "</td><td>"
                        . $db[$user]['win'] . "</td><td>"
                        . $db[$user]['loss'] . "</td></tr>");
                ?>


            </table>
        </div>
        
    </div>
</div>

<?php include('partial/footer.php'); ?>