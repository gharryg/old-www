<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/scripts/password_compact.php");

function getDB(){
    $con = mysql_connect("db", "gharryg", "iVnjc5ZYWP9vwhR3VP6DBpJD");
    if(!$con)	return die("Could not connect: " . mysql_error());
    else	return mysql_select_db("gharryg", $con);
}

//sign-in/registration functions----------------------------------------------------------------------------------------
function hashPassword($pw){
    $hashed = md5(crypt($pw, "NRQnzmVDPHaXuYZ5"));
    return $hashed;
}

function makeCode(){
    $characters = "abcdefghijklmnopqrstuvwxyz0123456789";
    $string = "";
    for ($i = 0; $i < 10; $i++){
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

function failedLoginAttempt($userID){
    getDB();
    $result = mysql_query("SELECT * FROM users WHERE id = '$userID'");
    $row = mysql_fetch_assoc($result);
    $attempts = intval($row['failedLoginAttempts']);
    $attempts++;
    mysql_query("UPDATE `users` SET failedLoginAttempts = '$attempts' WHERE id = '$userID'");
    return $attempts;
}

//admin functions-------------------------------------------------------------------------------------------------------
function getSiteStatus(){
    getDB();
    $result = mysql_query("SELECT * FROM settings WHERE id = 1");
    $row = mysql_fetch_assoc($result);
    return $row['value'];
}

function setSiteStatus($status, $headerMessage, $bodyMessage){
    getDB();
    mysql_query("UPDATE `settings` SET value = '$status' WHERE id = 1");
    mysql_query("UPDATE `settings` SET headerMessage = '$headerMessage' WHERE id = 1");
    mysql_query("UPDATE `settings` SET bodyMessage = '$bodyMessage' WHERE id = 1");
}

function logActivity($action, $id){
    return;
    getDB();
    $content = "[" . date("F j, Y", (time())) . " " . date("H:i:s", (time())) . "] [" . $_SERVER['REMOTE_ADDR'] . "] " . getFullNameByID($id) . " (" . $id . ") ";
    switch($action){
        case 'register':
            $content .= "has registered his/her account.";
            break;
        case 'verify':
            $content .= "has verified his/her account.";
            break;
        case 'signin':
            $content .= "has signed in.";
            break;
        case 'signout':
            $content .= "has signed out.";
            break;
        case 'signinAttempt':
            $content .= " tried to sign in.";
            break;
        default;
            $content .= "did something.";
            break;
    }
    $content .= "\n";
    $file = "/code/logs/activity.log";
    file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
}

//user info functions---------------------------------------------------------------------------------------------------
function getIDByEmail($email){
    getDB();
    $result = mysql_query("SELECT * FROM users WHERE email = '$email'");
    $row = mysql_fetch_assoc($result);
    if($row)	return $row['id'];
    else	return -1;
}

function getEmailByID($id){
    getDB();
    $result = mysql_query("SELECT * FROM users WHERE id = '$id'");
    $row = mysql_fetch_assoc($result);
    if($row)	return $row['email'];
    else	return null;
}

function getFullNameByID($id){
    if($id < 0) return "Unknown";
    getDB();
    $result = mysql_query("SELECT * FROM users WHERE id = '$id'");
    $row = mysql_fetch_assoc($result);
    return $row['firstName'] . " " . $row['lastName'];
}

//ban functions---------------------------------------------------------------------------------------------------------
function checkBan($id){
    getDB();
    $result = mysql_query("SELECT * FROM users WHERE id = '$id'");
    $row = mysql_fetch_assoc($result);
    return $row['banned'];
}

function banUser($id){
    getDB();
    mysql_query("UPDATE `users` SET banned = 1 WHERE id = '$id'");
}

function toggleBan($id){
    getDB();
    $result = mysql_query("SELECT * FROM users WHERE id = '$id'");
    $row = mysql_fetch_assoc($result);
    if($row['banned'] == 1) mysql_query("UPDATE `users` SET banned = 0 WHERE id = '$id'");
    if($row['banned'] == 0) mysql_query("UPDATE `users` SET banned = 1 WHERE id = '$id'");
}

//pente functions-------------------------------------------------------------------------------------------------------
function hasPlayedPente($id){
    getDB();
    $result = mysql_query("SELECT * FROM `pente` WHERE userID = '$id'");
    $row = mysql_fetch_assoc($result);
    if($row)    return true;
    else    return false;
}
?>
