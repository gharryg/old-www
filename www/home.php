<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="/styles/home.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
            <a href="projects/pente"><img src="images/home/pente.png" id="pente"></a>
            <div class="title">Want to learn how to code? It's easy.</div>
           	<section>
            	<table>
                	<tr>
                    	<td>
                            <div class="video-container">
                                <iframe width="100%" height="100%" src="https://www.youtube.com/embed/nKIu9yen5nc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </td>
                        <td id="video">
                        	<h2>Learn to code. &nbsp; Anybody can learn.</h2>
                            <p>See some programming examples at <a href="/projects">gharryg.com/projects</a>.<br><br>
                            &quot;Everybody in this country should learn how to program a computer...<br>
							because it teaches you how to think.&quot;<br>&dash;Steve Jobs<br><br>
							Visit <a href="http://code.org" target="_blank">code.org</a> and do some Googling to learn more.</p>
                        </td>
                    </tr>
                </table>
           	</section>
        	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
        </div>
	</body>
</html>
