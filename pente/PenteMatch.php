<?php

require_once("PenteTile.php");

class PenteMatch{
    public $id;
    public $firstPlayer;
    public $secondPlayer;

    public $board = array();

    function __construct($uniqueID, $user1, $user2){
        $this->id = $uniqueID;
        $this->firstPlayer = $user1;
        $this->secondPlayer = $user2;

        for($c = 1;$c <= 17;$c++){
            $this->board[$c] = array();
            for($r = 1;$r <= 17;$r++){
                $tile = new PenteTile($c, $r);
                $this->board[$c][$r] = $tile;
            }
        }
    }

    function toString(){
        $string = "";
        for($c = 1;$c <= 17;$c++){
            for($r = 1;$r <= 17;$r++){
                $string .= $this->board[$c][$r]->id . ",";
            }
        }
        return rtrim($string, ",");
    }

    function isLegal($tile, $id0){
        $toPlay = $tile->id;
        if($toPlay == 1)   $notToPlay = 2;
        else    $notToPlay = 1;
        for($i = 0; $i < 8; $i++){
            $neighbor = $this->getNeighbor($tile, $i);
            if($neighbor == null)   continue;
            try{
                //if(($id0 != 0) || (($neighbor->id == $toPlay) && ($this->getNeighbor($neighbor, $i)->id == $notToPlay) && ($this->getNeighbor($tile, $i + 4)->id == $notToPlay))) return false;
                if($id0 != 0)   return false;
            }
            catch(Exception $e){
            }
        }
        return true;
    }

    function play($tile, $color, $user){
        $c = $tile->c;
        $r = $tile->r;
        $this->board[$c][$r]->id = $color;
        if($this->wonByRun($tile)) return 1;
        $this->removePairs($tile, $user);
        if($this->wonByCaps($user)) return 1;
        return 0;
    }

    function wonByRun($tile){
        $runLength = 1;
        $dir = 0;
        while($runLength < 5){
            $neighbor0 = $this->getNeighbor($tile, $dir);
            if($neighbor0 == null || $neighbor0->id != $tile->id){
                $runLength = 1;
                if($dir >= 7)   break;
                else    $dir++;
                continue;
            }
            else    $runLength++;
            while($runLength < 5){
                $neighbor = $this->getNeighbor($neighbor0, $dir);
                if($neighbor == null || $neighbor->id != $neighbor0->id){
                    $reversedDir = $this->reverseDirection($dir);
                    $tile0 = clone $tile;
                    while($runLength < 5){
                        $neighbor = $this->getNeighbor($tile0, $reversedDir);
                        if($neighbor == null || $neighbor->id != $tile0->id){
                            $runLength = 1;
                            $dir++;
                            break 2;
                        }
                        else{
                            $runLength++;
                            $tile0 = $neighbor;
                        }
                    }
                }
                else{
                    $runLength++;
                    $neighbor0 = $neighbor;
                }
            }
        }
        if($runLength >= 5) return true;
        else    return false;
    }

    function reverseDirection($dir){
        if($dir == 0)   return 4;
        if($dir == 1)   return 5;
        if($dir == 2)   return 6;
        if($dir == 3)   return 7;
        if($dir == 4)   return 0;
        if($dir == 5)   return 1;
        if($dir == 6)   return 2;
        if($dir == 7)   return 3;
    }

    function wonByCaps($user){
        if($user->captures >= 5)    return true;
        return false;
    }

    function getNeighbor($tile, $ref){
        if($tile == null)   return null;
        switch($ref){
            case 0:
                if($tile->r < 17)   return $this->board[$tile->c][$tile->r + 1];
                else    return null;
                break;

            case 1:
                if($tile->c > 1 && $tile->r < 17)    return $this->board[$tile->c - 1][$tile->r + 1];
                else    return null;
                break;

            case 2:
                if($tile->c > 1)    return $this->board[$tile->c - 1][$tile->r];
                else    return null;
                break;

            case 3:
                if($tile->c > 1 && $tile->r > 1)    return $this->board[$tile->c - 1][$tile->r - 1];
                else    return null;
                break;

            case 4:
                if($tile->r > 1)    return $this->board[$tile->c][$tile->r - 1];
                else    return null;
                break;

            case 5:
                if($tile->c < 17 && $tile->r > 1)   return $this->board[$tile->c + 1][$tile->r - 1];
                else    return null;
                break;

            case 6:
                if($tile->c < 17)   return $this->board[$tile->c + 1][$tile->r];
                else    return null;
                break;

            case 7:
                if($tile->c < 17 && $tile->r < 17)  return $this->board[$tile->c + 1][$tile->r + 1];
                else    return null;
                break;
            default:
                return null;
        }

    }

    function removePairs($tile, $user){
        $trapDir = $this->trappedPairDir($tile);
        while($trapDir != -1){
            $this->removePairFromGrid($tile, $trapDir);
            if($this->firstPlayer->isTurn)  $this->firstPlayer->captures++;
            else    $this->secondPlayer->captures++;
            $trapDir = $this->trappedPairDir($tile);
            if($this->wonByCaps($user)) $trapDir = -1;
        }
    }

    function trappedPairDir($tile){
        $d = -1;
        $toPlay = $tile->id;
        if($toPlay == 1)   $notToPlay = 2;
        else    $notToPlay = 1;
        for($i = 0; $i < 8; $i++){
            $test = $this->getNeighbor($tile, $i);
            if($test == null || $test->id != $notToPlay)   continue;
            $test = $this->getNeighbor($test, $i);
            if($test->id != $notToPlay)   continue;
            if($this->getNeighbor($test, $i)->id == $toPlay)   $d = $i;
        }
	    return $d;
    }

    function removePairFromGrid($tile, $trapD){
        $first = $this->getNeighbor($tile, $trapD);
        $this->getNeighbor($first, $trapD)->id = 0;
        $first->id = 0;
    }
}