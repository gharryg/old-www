<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
	
	if(getSiteStatus() == 1 || $_SESSION['admin'] == 1)	echo("<script>window.location=\"/\"</script>");

    session_start();
    session_destroy();

    getDB();
    $result = mysql_query("SELECT * FROM settings WHERE id = 1");
    $row = mysql_fetch_assoc($result);

    $headerMessage = $row['headerMessage'];
    $message = $row['bodyMessage'];
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Site Down</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
       	<link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,300' rel='stylesheet' type='text/css'>
        <link rel="shortcut icon" type="image/x-icon" href="/images/global/favicon.ico">
        <script src="/scripts/siteDown.js"></script>
        <link href="/styles/sitedown.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <noscript>
            <div id="noscript">
                <img src="/images/noscript.jpg" width="500" height="350" alt="JavaScript is disabled."><br>
                Please enable JavaScript to see this page.<br>
                Thank you.
        	</div>
            <style type="text/css">#content{display:none;}</style>
        </noscript>
        <div id="content">
        	<section>
                <h1><?php echo("$headerMessage") ?></h1>
            	<img src="/images/global/sad-mac.gif">
            	<p id="message"><?php echo("$message") ?></p>
			</section>
            <footer>
                This site was created by Harrison Golden. &nbsp; <a onClick="show()">&copy;</a> 2012-2014 gharryg.com
                <br>
                <img id="html5Showcase" src="/images/global/html5-footer.png" height="48" alt="This site contains HTML5 features.">
                <a target="_blank" href="https://www.positivessl.com" style="font-family: arial; font-size: 10px; color: #212121; text-decoration: none;"><img id="ssl" src="https://www.positivessl.com/images-new/PositiveSSL_tl_trans2.gif" alt="SSL Certificate" title="SSL Certificate" border="0" /></a>
            </footer>
            <form id="login" action="/scripts/signinPost.php" method="post">
                E-mail: <input type="email" name="email">
                <br/>
                Password: <input type="password" name="password">
                <br/>
                <input id="submit" type="submit" value="Sign In">
            </form>
            <script>function show(){document.getElementById('login').style.display = "block";}</script>
        </div>
	</body>
</html>