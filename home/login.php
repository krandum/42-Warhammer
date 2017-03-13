<?php
    session_start();
    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header('Location: index.php');
        exit();
    }
    include('partial/head.php');
?>
<body class="small">
    <div class="reg-log">
        <div class="circle"></div>
        <h1>Log In</h1>
        <form action="controller/people.php" method="POST">
            <input type="text" name="username" placeholder="Username" class="" value="">
            <input type="password" name="password" placeholder="Password" class="">
            <button type="submit" class="btn btn-default">Log in</button>
            <input type="hidden" name="from" value="login">
            <input type="hidden" name="success" value="index">
            <p>Don't have an account yet? <a href="register.php">Register</a></p>
        </form>
    </div>