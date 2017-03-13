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

<body onload="setInterval('chat.update()', 1000)">
<div class="container">
    <div class="row">
        <div class="col-l-12" style="width:100%">
            <div id="page-wrap">
                <h2>Awesome Starship Chatroom</h2>
                <p id="name-area"></p>
                <div id="chat-wrap"><div id="chat-area"></div></daiv>
                <form id="send-message-area">
                    <p>Your message: </p>
                    <textarea id="sendie" maxlength = '100' ></textarea>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('partial/footer.php'); ?>