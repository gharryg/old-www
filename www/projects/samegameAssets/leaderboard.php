<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300' rel='stylesheet' type='text/css'>
        <link href="leaderboard.css" rel="stylesheet" type="text/css">
        <script src="http://code.jquery.com/jquery-latest.min.js"></script>
    </head>
    <body>
        <script>function clickedSeed(seed){window.parent.postMessage(seed, "*")}</script>
        <table id="leaderboardTable">
            <tr>
                <td style="width:5%">Rank</td>
                <td style="width:25%">Name</td>
                <td style="width:10%">Score</td>
                <td style="width:10%">Blocks Left</td>
                <td style="width:25%">Seed<small> &nbsp; (Click on a seed to play it.)</small></td>
                <td style="width:25%">Date/Time</td>
            </tr>
            <?php
                require_once($_SERVER['DOCUMENT_ROOT'] . "/required/utilities.php");
                session_start();
                getDB();

                if($_SESSION['admin'] == 1) echo("            <script>
                            function toggleBan(id){
                                var request = new XMLHttpRequest();
                                request.open(\"POST\", \"/admin/toggleBan.php?id=\" + id, false);
                                request.setRequestHeader(\"Content-type\", \"application/x-www-form-urlencoded\");
                                request.send();
                                setTimeout(window.location.reload(), 1000);
                            }
                        </script>");

                $result = mysql_query("SELECT * FROM `samegame` ORDER BY `score` DESC");
                $totalRows = mysql_num_rows($result);
                $rowCount = 1;
                $currentRank = 1;
                $totalRank = 1;
                $html = "";
                $row;
                $row0 = mysql_fetch_assoc($result);
                if($_SESSION['admin'] != 1){
                    while(checkBan($row0['userID']) == 1){
                        $row0 = mysql_fetch_assoc($result);
                        $rowCount++;
                    }
                }
                $score0 = -1;
                while($totalRows >= $rowCount){
                    $row = mysql_fetch_assoc($result);
                    if($_SESSION['admin'] != 1){
                        while(checkBan($row['userID']) == 1){
                            $row = mysql_fetch_assoc($result);
                            $rowCount++;
                        }
                    }
                    if($row0['blocksLeft'] == 0)    $html .= "            <tr class=\"clearedBoard\">";
                    else    $html .= "            <tr>";

                    if($score0 == $row0['score'])    $rankText = "T" . $currentRank;
                    else if($row0['score'] == $row['score']){
                        $currentRank = $totalRank;
                        $rankText = "T" . $currentRank;
                    }
                    else{
                        $currentRank = $totalRank;
                        $rankText = $currentRank;
                    }
                    $html .= "                <td>" . $rankText . "</td>";

                    if($_SESSION['admin'] == 1) $html .= "                <td class=\"seed\" onclick=\"toggleBan(" . $row0['userID'] . ")\">" . getFullNameByID($row0['userID']) . " &nbsp; Banned = " . checkBan($row0['userID']) . "</td>";
                    else    $html .= "                <td>" . getFullNameByID($row0['userID']) . "</td>";

                    $html .= "                <td>". $row0['score'] . "</td>";

                    if($row0['blocksLeft'] == 0)    $html .= "                <td><strong><i>" . $row0['blocksLeft'] . "</i></strong></td>";
                    else    $html .= "                <td>" . $row0['blocksLeft'] . "</td>";

                    $html .= "                <td class=\"seed\" title=\"Click to play this seed.\" onclick=\"clickedSeed('" . $row0['seed'] . "')\">" . $row0['seed'] . "</td>
                <td>" . date("F j, Y, g:i a", ($row0['timestamp'])) . "</td>";

                    $rowCount++;
                    $totalRank++;
                    $score0 = $row0['score'];
                    $row0 = $row;
                }
                if($rowCount == 1)    echo("        </table>
                            <h2 id=\"noScores\">No scores available.</h2>");
                else    echo $html . "        </table>";
            ?>
    </body>
</html>