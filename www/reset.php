<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
	
	session_destroy();
	session_start();
	
	$email = $_GET['email'];
	$code = $_GET['code'];
	
	$_SESSION['email'] = $email;
	$_SESSION['code'] = $code;
	
	getDB();
	$result = mysql_query("SELECT * FROM users WHERE email = '$email'");
	$row = mysql_fetch_assoc($result);
	
	if($row){
		if(time() < $row['resetExpireTime']){
			if($code == $row['resetCode']){
				$_SESSION['canReset'] = 1;
			}
			else{
				setcookie("forgotMessage", "Your reset link is invalid.<br>Please request a new link.", time() + 20, "/");
				echo("<script>window.location = \"/forgot\"</script>");
			}
		}
		else{
			setcookie("forgotMessage", "Your reset link has expired.<br>Please request a new link.", time() + 20, "/");
			echo("<script>window.location = \"/forgot\"</script>");
		}
	}
	else{
		setcookie("forgotMessage", "There is no account associated with this reset link.<br>Please request a new link.", time() + 20, "/");
		echo("<script>window.location = \"/forgot\"</script>");
	}
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Reset</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="styles/reset.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
        	<div class="title">Reset Your Password.</div>
            <section>
                <form action="/scripts/resetPost.php" method="post">
                <div class="message">Enter a new password.</div>
                	<table>
                        <tr><td>Password: <input type="password" name="password" autofocus></td></tr>
                        <tr><td>Re-type Password: <input type="password" name="password1"></td></tr>
                        <tr><td class="center"><input id="submit" type="submit" value="Reset Password"></td></tr>
                    </table>
                </form>
            </section>
			<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
        </div>
	</body>
</html>