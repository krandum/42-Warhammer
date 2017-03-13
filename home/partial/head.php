<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Awesome Starships Battles II</title>
    <link rel="stylesheet" href="style/style.css">
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    <?php
    	if (basename($_SERVER['PHP_SELF']) == 'chat.php')
    	{
    		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    		echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>';
            echo '<script>var name=' . json_encode($_SESSION['username'], JSON_HEX_TAG) . '</script>';
    		echo '<script src="js/chat.js"></script>';
    	}
    ?>
</head>
<body>