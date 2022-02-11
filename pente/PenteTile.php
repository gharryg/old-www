<?php

class PenteTile{
    public $c;
    public $r;
    public $id;

    function __construct($cPos, $rPos){
        $this->c = $cPos;   //x
        $this->r = $rPos;   //y
        $this->id = 0;
    }
}