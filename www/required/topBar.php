<div id="topBar">
            <div id="topBarMeat">
            	<div id="logo">
                    <a id="logoLink" href="/">gharryg.com</a>
                </div>
                <div id="siteLinks">
                    <a class="topLink" href="/">Home</a>
                    <a class="topLink" href="#">About</a>
                    <a class="topLink" href="/projects">Projects</a>
                    <a class="topLink" href="#">Minecraft Server</a>
                    <?php if($_SESSION['admin'] == 1) echo("<a class=\"topLink\" href=\"/admin\">Admin Tools</a>
                    <a class=\"topLink\" href=\"#\">phpMyAdmin</a>
                    <a class=\"topLink\" href=\"#\">Development</a>") ?>
                </div>
                <div id="userArea">
                    <div id="UAMeat">
                    	<?php
							if(isset($_SESSION['userID']))	echo("Welcome, " . $_SESSION['firstName'] . " " . $_SESSION['lastName'] . "!" . "<a class=\"UALink\" href=\"/scripts/signoutPost.php\"> Sign Out</a>");
							else echo("<a class=\"UALink\" href=\"/signin\">Sign In</a>
                        or
                        <a class=\"UALink\" href=\"/register\">Register</a>");
						?>
                    </div>
                </div>
            </div>
            <div id="topBarBorder"></div>
        </div>
