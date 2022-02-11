<?php
session_start();
if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1){
    require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
    $message = $_POST['message'];
    echo("<script>var messageB = '$message';
try{
    var host = 'ws://gharryg.com:8080';
    var PenteServer = new WebSocket(host);
    PenteServer.onopen = function(message){
        try{
            PenteServer.send('serverBroadcast-' + messageB);
        }
        catch(e){
        }
    };
    PenteServer.onmessage = function(message){};
    PenteServer.onclose = function(message){
        PenteServer = null;
    };
}
catch(e){
}
window.location = '/admin';</script>");
}
else{
    header("Location: /errors/404");
    die();
}
?>