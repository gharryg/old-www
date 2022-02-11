<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
?>
<!DOCTYPE>
<html>
    <head>
    	<title>gharryg.com | Access Denied</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="/styles/error.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
        	<section>
                <h1>Uh, oh.</h1>
                <img src="/images/global/sad-mac.gif"/>
                <br>
                <p>You are not allowed to access this page.
                <br>
                Click <a onClick="history.go(-1)">here</a> to go back.</p>
			</section>
			<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?> 
        </div>
	</body>
</html>