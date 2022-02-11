<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");

session_start();
getDB();

if(isset($_SESSION['admin']))   if($_SESSION['admin'] == 1){
    echo("<table><tr id='top'><td><strong>Rank</strong></td><td><strong>Name</strong></td><td><strong>Banned</strong></td><td><strong>Email</strong></td><td><strong>Elo</strong></td><td><strong>Total Wins</strong></td><td><strong>Total Losses</strong></td><td><strong>Winning Percentage</strong></td><td><strong>Current Win Streak</strong></td><td><strong>Longest Win Streak</strong></td></tr>");
    $result = mysql_query("SELECT * FROM `pente` ORDER BY `elo` DESC");
    $numRows = mysql_num_rows($result);
    for($i = 1;$i <= $numRows;$i++){
        $row = mysql_fetch_assoc($result);
        echo("<tr><td>" . $i . "</td><td>" . getFullNameByID($row['userID']) . "</td><td class='hover' onclick='toggleBan(" . $row['userID'] . ")'>" . checkBan($row['userID']) . "</td><td>" . getEmailByID($row['userID']) . "</td><td>" . $row['elo'] . "</td><td>" . $row['totalWins'] . "</td><td>" . $row['totalLosses'] . "</td><td>" . $row['winLossRatio'] . "</td><td>" . $row['currentWinRun'] . "</td><td>" . $row['longestWinRun'] . "</td></tr>");
    }
    echo("</table>");
    echo("<style>
    .hover:hover{background-color: darkgray;}
    .hover:active{background-color: gray;}
    </style>");
    echo("<script>
        function toggleBan(id){
            var request = new XMLHttpRequest();
            request.open(\"POST\", \"toggleBan.php?id=\" + id, false);
            request.setRequestHeader(\"Content-type\", \"application/x-www-form-urlencoded\");
            request.send();
            setTimeout(window.location.reload(), 1000);
        }
    </script>");
}
else{
    echo("<table><tr id='top'><td><strong>Rank</strong></td><td><strong>Name</strong></td><td><strong>Elo</strong></td><td><strong>Total Wins</strong></td><td><strong>Total Losses</strong></td><td><strong>Winning Percentage</strong></td><td><strong>Current Win Streak</strong></td><td><strong>Longest Win Streak</strong></td></tr>");
    $result = mysql_query("SELECT * FROM `pente` ORDER BY `elo` DESC");
    $numRows = mysql_num_rows($result);
    $ndx = 1;
    for($i = 1;$i <= $numRows;$i++){
        $row = mysql_fetch_assoc($result);
        if(!checkBan($row['userID'])){
            echo("<tr><td>" . $ndx . "</td><td>" . getFullNameByID($row['userID']) . "</td><td>" . $row['elo'] . "</td><td>" . $row['totalWins'] . "</td><td>" . $row['totalLosses'] . "</td><td>" . $row['winLossRatio'] . "</td><td>" . $row['currentWinRun'] . "</td><td>" . $row['longestWinRun'] . "</td></tr>");
            $ndx++;
        }
    }
    echo("</table>");
}
?>
<link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,300' rel='stylesheet' type='text/css'>
<style>
    html{
        width: 100%;
        font-family: "Titillium Web";
    }
    table{
        width: 100%;
        border-collapse: collapse;
    }
    table tr td{
        border-width: thin;
        border-style: solid;
        border-color: #000000;
    }
    #top{
        background-color: darkgrey;
    }
</style>