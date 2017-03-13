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
        <h1>Register</h1>
        <form action="controller/people.php" method="POST">
            <input type="text" name="username" placeholder="Username" value="" class="<?php echo isset($_POST['username']) ? 'error' : '' ; ?>">
            <input type="password" name="password" placeholder="Password" class="<?php echo isset($_POST['password']) ? 'error' : '' ; ?>">
            <button type="submit" class="btn btn-default">Register</button>
            <input type="hidden" name="from" value="register">
            <input type="hidden" name="success" value="login">
            <p>Already registered? <a href="login.php">Login</a></p>
        </form>
    </div>