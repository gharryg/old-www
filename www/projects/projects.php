<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Projects</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="/styles/projects.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
        	<div class="title">Projects.</div>
            <a href="pente"  class="linkWrapper">
                <img src="/images/projects/pente.png" class="link" alt="Pente.">
            </a>
            <a href="samegame"  class="linkWrapper">
                <img src="/images/projects/samegame.png" class="link">
            </a>
            <a href="topographer"  class="linkWrapper">
                <img src="/images/projects/topographer.png" class="link" alt="Topographical grapher.">
            </a>
        	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
        </div>
	</body>
</html>