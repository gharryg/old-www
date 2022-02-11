<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
    session_start();
    getDB();

    $function = $_GET['function'];
    $userID = $_GET['userID'];
    $board = $_GET['board'];
    $board0 = $_GET['board0'];
    $blocksLeft = $_GET['blocksLeft'];
    $seed = $_GET['seed'];
    $selection = $_GET['selection'];

    $referer = $_SERVER['HTTP_REFERER'];

    if($referer != "http://localhost/projects/samegame") echo("<script>window.location = \"/errors/404\"</script>");
    else{
        switch($function){
            case 'getScore':
                getScore();
                break;
            case 'getScore0':
                getScore0();
                break;
            case 'postScores':
                putScores();
                break;
            case 'clearScores':
                clearScores();
                break;
            case 'getBoard':
                getBoard();
                break;
            case 'putBoards':
                putBoards($board, $board0);
                break;
            case 'putSeed':
                putSeed($seed);
                break;
            case 'getSeed':
                getSeed();
                break;
            case 'submitGame':
                submitGame($blocksLeft);
                break;
            case 'putSelection':
                putSelection($selection);
                break;
            case 'undoScore':
                undoScore();
                break;
            default:
                echo -1;
        }
    }

    function getScore(){
        if(isset($_SESSION['score']))   echo $_SESSION['score'];
        else return -1;
    }

    function getScore0(){
        if(isset($_SESSION['score0']))   echo $_SESSION['score0'];
        else return -1;
    }

    function putScores(){
       $_SESSION['score0'] = $_SESSION['score'];
       $_SESSION['score'] = $_SESSION['score'] + $_SESSION['selection'];
       $_SESSION['selection'] = 0;
       echo $_SESSION['score'];
    }

    function getBoard(){
        if(isset($_SESSION['board']))   echo $_SESSION['board'];
        else return -1;
    }

    function getBoard0(){
        if(isset($_SESSION['board0']))   echo $_SESSION['board0'];
        else return -1;
    }

    function putBoards($board, $board0){
        $_SESSION['board'] = $board;
        $_SESSION['board0'] = $board0;
    }

    function putSeed($seed){
        $_SESSION['seed'] = $seed;
    }

    function getSeed(){
        if(isset($_SESSION['seed']))   echo $_SESSION['seed'];
        else return -1;
    }

    function putSelection($selection){
        $_SESSION['selection'] = $selection;
    }

    function submitGame($blocksLeft){
        $score = $_SESSION['score'];
        $seed = $_SESSION['seed'];
        $userID = $_SESSION['userID'];
        if($userID > 0) mysql_query("INSERT INTO samegame (userID, score, blocksLeft, seed, timestamp) VALUES ('$userID', '$score', '$blocksLeft', '$seed', " . time() .")");
        $_SESSION['score'] = 0;
    }

    function clearScores(){
        $_SESSION['score'] = 0;
        $_SESSION['score0'] = 0;
    }

    function undoScore(){
        $_SESSION['score'] = $_SESSION['score0'];
    }
?>