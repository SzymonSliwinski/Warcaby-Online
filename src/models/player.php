<?php
    class Player{
        public $id;
        public $nick;
        public $color;
        public $checkersLeft;

        public function __construct($id, $nick, $color){
            $this->id = $id;
            $this->nick = $nick;
            $this->color = $color;
            $this->checkersLeft = 12;
        }
    }
?>