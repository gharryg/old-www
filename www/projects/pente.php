<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");

if(!isset($_SESSION['userID'])){
    setcookie("signinMessage", "You must be signed in to view this page.", time() + 20, "/");
    echo("<script>window.location = \"/signin\"</script>");
}
else{
    $id = $_SESSION['userID'];
    if(!hasPlayedPente($id)){
        getDB();
        mysql_query("INSERT INTO `pente` (userID) VALUES ($id)");
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>gharryg.com | Pente</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <link href="penteAssets/pente.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="penteAssets/js/penteClient.js"></script>
        <script type="text/javascript" src="penteAssets/js/wat.js"></script>
        <script type="text/javascript" src="penteAssets/js/detectmobilebrowser.js"></script>
    </head>
    <body>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
            <div class="title">Pente.</div>
            <div id="message"></div>
            <div id="board">
                <img id="overlay" src="penteAssets/images/howToPlay.png" onclick="joinQueue()">
                <div id="info">
                    <p id="player">0</p>
                    <p id="status"></p>
                    <p id="opponent">0</p>
                </div>
                <canvas id="pieces" width="720" height="720"></canvas>
            </div>
            <section id="below">
                <h2 id="queueTitle">Player Queue</h2>
                <small id="totalWrapper">Total players online: <strong id="total">0</strong></small>
                <p id="queueStatus"></p>
                <div id="queueWrapper">
                    <table id="queue">
                        <tr><td class="noBorder">The queue will appear when you join the queue.</td></tr>
                    </table>
                </div>
                <table id="gameInfo">
                    <tr>
                        <td>
                            <h2>Game Rules</h2>
                            <ol>
                                <h4><i>Any violation of these rules could result in a ban.</i></h4>
                                <li><strong>If you are on a mobile device, </strong>do not exit the Pente window/tab. You will be disconnected if you bring another application or webpage into focus.</li>
                                <li>Do not stall when playing a game. You will be automatically disconnected after 5 minutes of inactivity.</li>
                                <li>Any attempt to have two active Pente connections on a single account will result in an automatic ban.</li>
                                <li>No quitting or disconnecting during a game. Doing so will result in a loss.</li>
                                <li>Trading wins with other players is not allowed.</li>
                                <li>gharryg.com accounts are limited to one per person.</li>
                            </ol>
                            <h2>Credits</h2>
                            <ul>
                                <li>Code - Harrison Golden</li>
                                <li>Graphics - Kyle O'Neal</li>
                                <li>Original Game Logic - James Tolson</li>
                            </ul>
                        </td>
                        <td>
                            <h2>Leaderboard</h2>
                            <iframe id="leaderboard" src="penteAssets/php/leaderboard.php" seamless="seamless"></iframe>
                        </td>
                    </tr>
                </table>
                <small>Copyright (c) 2012, Adam Alexander<br>
                    All rights reserved.<br>
                    Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:<br>
                    <ul>
                        <li>Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.</li>
                        <li>Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.</li>
                        <li>Neither the name of PHP WebSockets nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.</li>
                    </ul>
                    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
                </small>
            </section>
            <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?>
            <div id="playerID"><?php echo($_SESSION['userID']) ?></div>
        </div>
    </body>
</html>