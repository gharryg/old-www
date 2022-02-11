<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
	
	getDB();

    session_start();
	
	if(isset($_SESSION['userID'])){
		$id = $_SESSION['userID'];
		$firstName = $_SESSION['firstName'];
		$lastName = $_SESSION['lastName'];
		
		$result = mysql_query("SELECT * FROM samegame WHERE userID = '$id' ORDER  BY score DESC");
		$row = mysql_fetch_assoc($result);
		if($row)	$personalBest = $row['score'];
		else	$personalBest = 0;
	}
	else{
		$id = -1;
		$firstName = -1;
		$lastName = -1;
		$personalBest = 0;
	}
	
	$result1 = mysql_query("SELECT * FROM samegame ORDER  BY score DESC");
	$row1 = mysql_fetch_assoc($result1);
	if($row1)	$highScore = $row1['score'];
	else	$highScore = 0;
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Same Game</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="samegameAssets/samegame.css" rel="stylesheet" type="text/css">
        <script src="/scripts/jquery.transit.min.js"></script>
        <script src="samegameAssets/seedrandom.js"></script>
        <script src="samegameAssets/samegame-source.js"></script>
    </head>
    <body onLoad="init(<?php echo("'$firstName'") ?>, <?php echo("'$lastName'") ?>, <?php echo("'$highScore'") ?>, <?php echo("'$personalBest'") ?>);">
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
        	<div class="title">Same Game.</div>
        	<section>
                <div id="gameInfo">
                    <div id="scores">
                        <h3>Scores</h3>
                        <table border="1" id="scoresTable">
                            <tr>
                                <td>Current<br/>Selection<br/>Value</td>
                                <td>Your<br/>Score</td>
                                <td>Overall<br/>High<br/>Score</td>
                                <td>Personal<br/>Best</td>
                            </tr>
                            <tr>
                                <td width="60px" id="csv">0</td>
                                <td width="60px" id="ys">0</td>
                                <td width="60px" id="hs"><?php echo("$highScore") ?></td>
                                <td width="60px" id="pb"><?php echo("$personalBest") ?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="blocks">
                        <h3>Blocks</h3>
                        <table border="1" id="blocksTable">
                            <tr>
                                <td><img src="samegameAssets/blocks/blockA.jpg"></td>
                                <td><img src="samegameAssets/blocks/blockB.jpg"></td>
                                <td><img src="samegameAssets/blocks/blockC.jpg"></td>
                                <td><img src="samegameAssets/blocks/blockD.jpg"></td>
                                <td><img src="samegameAssets/blocks/blockE.jpg"></td>
                                <td>Blocks<br>left:</td>
                            </tr>
                            <tr>
                                <td width="60px" id="a">0</td>
                                <td width="60px" id="b">0</td>
                                <td width="60px" id="c">0</td>
                                <td width="60px" id="d">0</td>
                                <td width="60px" id="e">0</td>
                                <td width="60px" id="bl">0</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <h3 id="seed">Board Seed: N/A</h3>
                <div id="gameWrapper"><p id="loading">Loading...</p><canvas id="game" width="800" height="400"></canvas></div>
                <div id="controls">
                    <small>Don't know what the buttons do? Hover over them to see a tip.</small><br>
                    <input id="reset" type="button" title="Resets your current board." value="New Game" onClick="startGame()"/>
                    <input id="undo" type="button" title="Undoes your last move." value="Undo" onClick="undo()" disabled/><br>
                    <input id="randomSeed" type="button" title="Generates a random seed." value="Random Seed" onClick="randomSeed()"/>
                    <input id="customSeed" type="button" title="Lets you enter a custom seed." value="Custom Seed" onClick="customSeed()"/>
                </div>
                <h3>Leaderboard</h3>
                <iframe id="leaderboard" width="100%" height="300px" src="samegameAssets/leaderboard.php" seamless frameBorder="0"></iframe>
            </section>
			<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?> 
        </div>
	</body>
</html>
