#! /usr/local/bin/php
<?php

require_once("WebSocketServer.php");
require_once("PenteMatch.php");

class PenteServer extends WebSocketServer{
    public $matches = array();
    public $queue = array();
    public $queueNdx = 0;
    public $mySQLConnection;
    public $database;

    protected function process($user, $message){
        $this->stdout("Message: " . $message);
        $data = explode('-', $message);
        switch($data[0]){
            case 'id':
                if($user->dbID == null){
                    $user->dbID = intval($data[1]);
                    $user->name = $this->getFullNameByID($user->dbID);
                    $user->elo = $this->getElo($user->dbID);
                    $user->totalWins = $this->getWins($user->dbID);
                    $user->totalLosses = $this->getLosses($user->dbID);
                    if(!$this->checkForUser($user)) $this->stdout("Updated " . $user->name . "'s (" . $user->id . ", " . $user->dbID . ") user information.");
                }
                break;
            case 'queue':
                if(!$user->isPlaying)    $this->addToQueue($user);
                break;
            case 'click':
                if($user->isPlaying)    $this->click($user, $data[1], $data[2]);
                break;
            case 'request':
                if($user->isInQueue)    $this->requestFriend($user, $data[1]);
                break;
            case 'serverBroadcast':
                $this->sendBroadcast($data[1]);
                $user->name = "Broadcast";
                $this->disconnect($user->socket);
                break;
            default:
                break;
        }
    }

    protected function connected($user){
        $this->stdout("User " . $user->id . " has connected.");
        $this->sendTotal();
    }

    function sendTotal(){
        $total = count($this->users);
        foreach($this->users as $tempUser){
            try{
                $this->send($tempUser, "total-" . $total);
            }
            catch(Exception $e){
                $this->stderr("Could not send message to user.\n" . $e->getMessage());
            }
        }
    }

    function addToQueue($user){
        $this->queue[$this->queueNdx] = $user;
        $user->queueNdx = $this->queueNdx;
        $this->queueNdx++;
        $user->isInQueue = true;
        $this->stdout($user->name . " (" . $user->id . ", " . $user->dbID . ") has been added to the queue.");
        try{
            $this->send($user, "queue");
        }
        catch(Exception $e){
            $this->stderr("Could not send message to user.\n" . $e->getMessage());
        }
        $this->sendUpdatedQueue();
    }

    function sendUpdatedQueue(){
        $string = "list-<tr><td id='top'><strong>Player Name ~ Elo</strong></td>";
        $ndx = 1;
        foreach($this->queue as $tempUser){
            if($ndx % 6 === 0)  $string .= "</tr><tr><td id='top'><strong>Player Name ~ Elo</strong></td>";
            $string .= "<td class='queueItem' onclick='sendFriendRequest(" . $tempUser->queueNdx . ")'>" . $tempUser->name . " ~ " . $tempUser->elo . "</td>";
            $ndx++;
        }
        $string .= "</tr>";
        foreach($this->queue as $tempUser){
            try{
                $this->send($tempUser, $string);
            }
            catch(Exception $e){
                $this->stderr("Could not send message to user.\n" . $e->getMessage());
            }
        }
    }

    function sendBroadcast($message){
        $this->stdout("Sending broadcast with message: " . $message);
        foreach($this->users as $tempUser){
            try{
                $this->send($tempUser, "broadcast-Message from site admin: " . $message);
            }
            catch(Exception $e){
                $this->stderr("Could not send message to user.\n" . $e->getMessage());
            }
        }
    }

    function createMatch($player1, $player2){
        $matchID = uniqid();
        $rng = rand(0, 1);
        try{
            if($rng == 1){
                $this->send($player1, "match-1-" . $player2->name);
                $this->send($player2, "match-2-" . $player1->name);
                $match = new PenteMatch($matchID, $player1, $player2);
                $this->matches[$matchID] = $match;
                unset($this->queue[$player1->queueNdx]);
                $this->stdout($player1->name . " (" . $player1->id . ", " . $player1->dbID . ") has been removed from the queue.");
                unset($this->queue[$player2->queueNdx]);
                $this->stdout($player2->name . " (" . $player2->id . ", " . $player2->dbID . ") has been removed from the queue.");
                $this->sendUpdatedQueue();

                $player1->isInQueue = false;
                $player1->matchID = $matchID;
                $player1->isTurn = true;
                $player1->color = 1;
                $player1->isPlaying = true;
                $player1->friendRequest = null;

                $player2->isInQueue = false;
                $player2->matchID = $matchID;
                $player2->color = 2;
                $player2->isPlaying = true;
                $player2->friendRequest = null;
            }
            else{
                $this->send($player1, "match-2-" . $player2->name);
                $this->send($player2, "match-1-" . $player1->name);
                $match = new PenteMatch($matchID, $player1, $player2);
                $this->matches[$matchID] = $match;
                unset($this->queue[$player1->queueNdx]);
                unset($this->queue[$player2->queueNdx]);
                $this->sendUpdatedQueue();

                $player1->isInQueue = false;
                $player1->matchID = $matchID;
                $player1->color = 2;
                $player1->isPlaying = true;
                $player1->friendRequest = null;

                $player2->isInQueue = false;
                $player2->matchID = $matchID;
                $player2->isTurn = true;
                $player2->color = 1;
                $player2->isPlaying = true;
                $player2->friendRequest = null;
            }
            $this->stdout("Match " . $matchID . " created with " . $player1->name . " (" . $player1->id . ", " . $player1->dbID . ") and " . $player2->name . " (" . $player2->id . ", " . $player2->dbID . ").");
        }
        catch(Exception $e){
            $this->stderr("Could not create match.\n" . $e->getMessage());
        }
    }

    function requestFriend($user, $queueID){
        if($user->queueNdx == $queueID){
            try{
                $this->send($user, "badRequest");
            }
            catch(Exception $e){
                $this->stderr("Could not send message to user.\n" . $e->getMessage());
            }
        }
        else{
            $friend = $this->queue[$queueID];
            $this->stdout($user->name . " (" . $user->id . ", " . $user->dbID . ") has requested to play with " . $friend->name . " (" . $friend->id . ", " . $friend->dbID . ").");
            if($friend->friendRequest == $user->queueNdx){
                $this->createMatch($user, $friend);
                foreach($this->queue as $tempUser){
                    if($tempUser->friendRequest == $user->queueNdx){
                        try{
                            $this->send($tempUser, "backstabber");
                        }
                        catch(Exception $e){
                            $this->stderr("Could not send message to user.\n" . $e->getMessage());
                        }
                    }
                }
            }
            else{
                $user->friendRequest = $queueID;
                try{
                    $this->send($friend, "request-" . $user->name);
                }
                catch(Exception $e){
                    $this->stderr("Could not send message to user.\n" . $e->getMessage());
                }
            }
        }
    }

    protected function closed($user){
        if($user->dbID == null) $this->stderr("An unregistered user (" . $user->id . ") has disconnected.");
        else{
            $this->stdout($user->name . " (" . $user->id . ", " . $user->dbID . ") has disconnected.");
            $this->sendTotal();
            if($user->isInQueue){
                unset($this->queue[$user->queueNdx]);
                $this->stdout($user->name . " (" . $user->id . ", " . $user->dbID . ") has been removed from the queue.");
                $this->sendUpdatedQueue();
                foreach($this->queue as $tempUser){
                    if($tempUser->friendRequest == $user->queueNdx){
                        try{
                            $this->send($tempUser, "backstabber");
                        }
                        catch(Exception $e){
                            $this->stderr("Could not send message to user.\n" . $e->getMessage());
                        }
                    }
                }
            }
            elseif($user->matchID != ""){
                $ndx = 0;
                foreach($this->matches as $tempMatch){
                    if($user->matchID == $tempMatch->id){
                        $firstPlayer = $tempMatch->firstPlayer;
                        $secondPlayer = $tempMatch->secondPlayer;
                        if($user->id == $firstPlayer->id){
                            $secondPlayer->isPlaying = false;
                            $secondPlayer->matchID = "";
                            $this->send($secondPlayer, "opponentDC");
                            $this->adjustRankings($secondPlayer, $firstPlayer);
                        }
                        if($user->id == $secondPlayer->id){
                            $firstPlayer->isPlaying = false;
                            $firstPlayer->matchID = "";
                            $this->send($firstPlayer, "opponentDC");
                            $this->adjustRankings($firstPlayer, $secondPlayer);
                        }
                        unset($this->matches[$ndx]);
                        $this->stdout("Match " . $tempMatch->id . " between " . $firstPlayer->name . " (" . $firstPlayer->id . ", " . $firstPlayer->dbID . ") and " . $secondPlayer->name . " (" . $secondPlayer->id . ", " . $secondPlayer->dbID . ") has been removed because " . $user->name . " disconnected.");
                        break;
                    }
                    $ndx++;
                }
            }
        }
    }

    function endMatch($match, $winner, $loser){
        $this->adjustRankings($winner, $loser);

        $id = $match->id;

        $firstPlayer = $match->firstPlayer;
        $firstPlayer->matchID = "";
        $firstPlayer->isPlaying = false;
        $firstPlayer->isTurn = false;
        $firstPlayer->captures = 0;
        $firstPlayer->color = null;

        $secondPlayer = $match->secondPlayer;
        $secondPlayer->matchID = "";
        $secondPlayer->isPlaying = false;
        $secondPlayer->isTurn = false;
        $secondPlayer->captures = 0;
        $secondPlayer->color = null;

        unset($this->matches[$id]);
        $this->stdout("Match " . $id . " between " . $firstPlayer->name . " (" . $firstPlayer->id . ", " . $firstPlayer->dbID . ") and " . $secondPlayer->name . " (" . $secondPlayer->id . ", " . $secondPlayer->dbID . ") has been removed because " . $winner->name . " defeated " . $loser->name . ".");
    }

    function click($user, $xString, $yString){
        if($user->isTurn && $user->isPlaying){
            $x = intval($xString);
            $y = intval($yString);

            $match = $this->matches[$user->matchID];
            $board = $match->board;
            $tile = $board[$x][$y];

            $result = $this->processBoard($match, $tile, $user);

            if($result == -1){
                try{
                    $this->send($user, "badMove");
                }
                catch(Exception $e){
                    $this->stderr("Could not send message to user.\n" . $e->getMessage());
                }
            }
            else{
                $boardString = $this->matches[$user->matchID]->toString();
                $user->isTurn = false;

                if($user->id == $match->firstPlayer->id)    $match->secondPlayer->isTurn = true;
                else    $match->firstPlayer->isTurn = true;

                try{
                    $this->send($match->firstPlayer, "movePlayed-" . $boardString . "," . $x . "," . $y . "-" . $result);
                    $this->send($match->secondPlayer, "movePlayed-" . $boardString . "," . $x . "," . $y . "-" . $result);
                }
                catch(Exception $e){
                    $this->stderr("Could not send board to user.\n" . $e->getMessage());
                }

                if($result == 1){
                    if($user->id == $match->firstPlayer->id)    $this->endMatch($match, $user, $match->secondPlayer);
                    if($user->id == $match->secondPlayer->id)    $this->endMatch($match, $user, $match->firstPlayer);
                }
            }
        }
    }

    function processBoard($match, $tile, $user){
        $id0 = $tile->id;
        $tile->id = $user->color;
        $captures0 = $user->captures;
        if($match->isLegal($tile, $id0)){
            $result = $match->play($tile, $user->color, $user);
            if($user->captures > $captures0){
                try{
                    $this->send($match->firstPlayer, "capture-" . $user->captures);
                    $this->send($match->secondPlayer, "capture-" . $user->captures);
                }
                catch(Exception $e){
                    $this->stderr("Could not send board to user.\n" . $e->getMessage());
                }
            }
            if($result == 1)    return 1;
        }
        else{
            $tile->id = $id0;
            return -1;
        }
        return 0;
    }

    function checkForUser($user){
        foreach($this->users as $tempUser){
            if($tempUser->id != $user->id && $tempUser->dbID == $user->dbID){
                try{
                    $this->send($user, "ban");
                    $this->send($tempUser, "ban");
                    banUser(intval($user->dbID));
                    $this->stdout($user->name . "(" . $user->id . ", " . $user->dbID . ") has been banned.");
                }
                catch(Exception $e){
                    $this->stderr("Could not send message to user.\n" . $e->getMessage());
                }
            }
        }
        return false;
    }

    function adjustRankings($winner, $loser){
        $winner->totalWins++;
        $this->setWins($winner->totalWins, $winner->dbID);
        $currentWinStreak = $this->getCurrentWinStreak($winner->dbID);
        $currentWinStreak++;
        $this->setCurrentWinStreak($currentWinStreak, $winner->dbID);
        $longestWinStreak = $this->getLongestWinStreak($winner->dbID);
        if($currentWinStreak > $longestWinStreak)   $this->setLongestWinStreak($currentWinStreak, $winner->dbID);
        $this->updateWinLossRatio($winner->dbID);

        $loser->totalLosses++;
        $this->setLosses($loser->totalLosses, $loser->dbID);
        $this->setCurrentWinStreak(0, $loser->dbID);
        $this->updateWinLossRatio($loser->dbID);

        $eloChange = 0;
        $difference = abs($winner->elo - $loser->elo);
        if($winner->elo == $loser->elo)    $eloChange = 16;
        elseif($winner->elo > $loser->elo){
            switch($difference){
                case ($difference < 25):
                    $eloChange = 16;
                    break;
                case ($difference < 50):
                    $eloChange = 15;
                    break;
                case ($difference < 75):
                    $eloChange = 14;
                    break;
                case ($difference < 100):
                    $eloChange = 13;
                    break;
                case ($difference < 125):
                    $eloChange = 12;
                    break;
                case ($difference < 150):
                    $eloChange = 11;
                    break;
                case ($difference < 175):
                    $eloChange = 10;
                    break;
                case ($difference < 200):
                    $eloChange = 9;
                    break;
                case ($difference < 225):
                    $eloChange = 8;
                    break;
                case ($difference < 250):
                    $eloChange = 7;
                    break;
                case ($difference < 275):
                    $eloChange = 6;
                    break;
                case ($difference < 300):
                    $eloChange = 5;
                    break;
                case ($difference >= 300):
                    $eloChange = 4;
                    break;
                default:
                    break;
            }
        }
        elseif($winner->elo < $loser->elo){
            switch($difference){
                case ($difference < 25):
                    $eloChange = 16;
                    break;
                case ($difference < 50):
                    $eloChange = 17;
                    break;
                case ($difference < 75):
                    $eloChange = 18;
                    break;
                case ($difference < 100):
                    $eloChange = 19;
                    break;
                case ($difference < 125):
                    $eloChange = 20;
                    break;
                case ($difference < 150):
                    $eloChange = 21;
                    break;
                case ($difference < 175):
                    $eloChange = 22;
                    break;
                case ($difference < 200):
                    $eloChange = 23;
                    break;
                case ($difference < 225):
                    $eloChange = 24;
                    break;
                case ($difference < 250):
                    $eloChange = 25;
                    break;
                case ($difference < 275):
                    $eloChange = 26;
                    break;
                case ($difference < 300):
                    $eloChange = 27;
                    break;
                case ($difference >= 300):
                    $eloChange = 28;
                    break;
                default:
                    break;
            }
        }
        $winner->elo += $eloChange;
        $this->setElo($winner->elo, $winner->dbID);
        $loser->elo -= $eloChange;
        $this->setElo($loser->elo, $loser->dbID);
        $this->sendReload();
    }

    function sendReload(){
        foreach($this->users as $tempUser){
            try{
                $this->send($tempUser, "reload");
            }
            catch(Exception $e){
                $this->stderr("Could not send message to user.\n" . $e->getMessage());
            }
        }
    }


    //database functions------------------------------------------------------------------------------------------------
    function checkDB(){
        $result = mysql_query("SELECT * FROM users WHERE id = 1");
        if(!$result){
            $this->stderr("Database connection lost. Attempting to establishing new connection.");
            $this->mySQLConnection = mysql_connect("db", "gharryg", "iVnjc5ZYWP9vwhR3VP6DBpJD", MYSQL_CLIENT_INTERACTIVE) or die("Could not connect: " . mysql_error());
            $this->database = mysql_select_db("gharryg", $this->mySQLConnection);
        }
    }

    function getFullNameByID($id){
        $this->checkDB();
        $result = mysql_query("SELECT * FROM users WHERE id = '$id'");
        $row = mysql_fetch_assoc($result);
        return $row['firstName'] . " " . $row['lastName'];
    }

    function setElo($newElo, $id){
        $this->checkDB();
        mysql_query("UPDATE `pente` SET elo = $newElo WHERE userID = $id");
    }

    function getElo($id){
        $this->checkDB();
        $result = mysql_query("SELECT * FROM `pente` WHERE userID = $id");
        $row = mysql_fetch_assoc($result);
        return $row['elo'];
    }

    function setWins($newWins, $id){
        $this->checkDB();
        mysql_query("UPDATE `pente` SET totalWins = $newWins WHERE userID = '$id'");
    }

    function setLosses($newLosses, $id){
        $this->checkDB();
        mysql_query("UPDATE `pente` SET totalLosses = $newLosses WHERE userID = '$id'");
    }

    function getWins($id){
        $this->checkDB();
        $result = mysql_query("SELECT * FROM `pente` WHERE userID = $id");
        $row = mysql_fetch_assoc($result);
        return $row['totalWins'];
    }

    function getLosses($id){
        $this->checkDB();
        $result = mysql_query("SELECT * FROM `pente` WHERE userID = $id");
        $row = mysql_fetch_assoc($result);
        return $row['totalLosses'];
    }

    function getLongestWinStreak($id){
        $this->checkDB();
        $result = mysql_query("SELECT * FROM `pente` WHERE userID = $id");
        $row = mysql_fetch_assoc($result);
        return $row['longestWinRun'];
    }

    function setLongestWinStreak($newStreak, $id){
        $this->checkDB();
        mysql_query("UPDATE `pente` SET longestWinRun = $newStreak WHERE userID = '$id'");
    }

    function getCurrentWinStreak($id){
        $this->checkDB();
        $result = mysql_query("SELECT * FROM `pente` WHERE userID = $id");
        $row = mysql_fetch_assoc($result);
        return $row['currentWinRun'];
    }

    function setCurrentWinStreak($newStreak, $id){
        $this->checkDB();
        mysql_query("UPDATE `pente` SET currentWinRun = $newStreak WHERE userID = '$id'");
    }

    function updateWinLossRatio($id){
        $this->checkDB();
        $wins = $this->getWins($id);
        $losses = $this->getLosses($id);
        if($wins == $losses)    mysql_query("UPDATE `pente` SET winLossRatio = '.500' WHERE userID = '$id'");
        elseif($losses <= 0)    mysql_query("UPDATE `pente` SET winLossRatio = '1.000' WHERE userID = '$id'");
        elseif($wins <= 0)    mysql_query("UPDATE `pente` SET winLossRatio = '0.000' WHERE userID = '$id'");
        else{
            $winLoss = round(($wins / ($losses + $wins)), 3);
            if(strlen($winLoss) < 5){
                $num = 5 - (strlen($winLoss));
                for($i = 0; $i < $num; $i++){
                    $winLoss .= "0";
                }
            }
            mysql_query("UPDATE `pente` SET winLossRatio = '$winLoss' WHERE userID = '$id'");
            return $winLoss;
        }
    }
}

$pente = new PenteServer("0.0.0.0", "8008");
$pente->mySQLConnection = mysql_connect("db", "gharryg", "iVnjc5ZYWP9vwhR3VP6DBpJD", MYSQL_CLIENT_INTERACTIVE) or die("Could not connect: " . mysql_error());
$pente->database = mysql_select_db("gharryg", $pente->mySQLConnection);

try{
    $pente->run();
}
catch(Exception $e){
    $pente->stderr("Could not start server.\n" . $e->getMessage());
}
