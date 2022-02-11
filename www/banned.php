<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
    session_start();
    session_destroy();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>gharryg.com | Banned</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="shortcut icon" type="image/x-icon" href="/images/global/favicon.ico">
        <link href="/styles/global.css" rel="stylesheet" type="text/css">
        <link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,300' rel='stylesheet' type='text/css'>
        <script src="https://code.jquery.com/jquery-latest.min.js"></script>
        <script src="/scripts/jquery.transit.min.js"></script>
        <link href="/styles/error.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
    <div id="content">
        <section>
            <h1>You've been banned.</h1>
            <img src="/images/global/sad-mac.gif"/>
            <br>
            <p>Contact a site admin to learn more.<br>
            Click <a href="/" style="color:white">here</a> to go home.</p>
        </section>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
    </div>
    </body>
</html>