<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

if(!isset($_SESSION['admin']) || $_SESSION['admin'] != 1){
    header("Location: /errors/404");
    die();
}

getDB();

$result = mysql_query("SELECT * FROM settings WHERE id = 1");
$row = mysql_fetch_assoc($result);
$headerMessage = $row['headerMessage'];
$bodyMessage = $row['bodyMessage'];

$result1 = mysql_query("SELECT * FROM activity ORDER BY `id` DESC");
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Admin Tools</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="/styles/admin.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
        	<div class="title">Admin Tools.</div>
        	<section>
                <table id="tools">
                    <tr>
                        <td id="status">
                            <h2>Site Status</h2>
                            <form action="updateSiteStatus.php" method="post">
                                Header Message: <input type="text" name="headerMessage" value="<?php echo("$headerMessage") ?>" size="35"><br>
                                Body Message: <input type="text" name="bodyMessage" value="<?php echo("$bodyMessage") ?>"size="35"><br>
                                <input type="radio" name="status" value="1" <?php if(getSiteStatus()) echo("checked=\"checked\"") ?>>ON<br>
                                <input type="radio" name="status" value="0" <?php if(!getSiteStatus()) echo("checked=\"checked\"") ?>>OFF<br>
                                <input type="submit" value="Update">
                            </form>
                        </td>
                        <td id="broadcast">
                            <h2>Pente Broadcast</h2>
                            <form action="broadcast.php" method="post">
                                Message: <input type="text" name="message"><br>
                                <input type="submit" value="Broadcast">
                            </form>
                        </td>
                        <td id="ban">
                            <h2>Toggle Ban</h2>
                            <form action="toggleBan.php" method="post">
                                User Email: <input type="email" name="email"><br>
                                <input type="submit" value="Toggle Ban">
                            </form>
                        </td>
                    </tr>
                </table>
           	</section>
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?> 
        </div>
     </body>
</html>