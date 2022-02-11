<?php

require_once("WebSocketUser.php");

class PenteUser extends WebSocketUser{
    public $isInQueue = true;
    public $isPlaying = false;
    public $isTurn = false;

    public $name = "";
    public $dbID = null;

    public $matchID = "";
    public $color;
    public $captures = 0;

    public $friendRequest;
    public $queueNdx;

    public $elo;
    public $totalWins;
    public $totalLosses;
}