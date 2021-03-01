<?php
    class GameRoomState{
        public $whoseTurn;
        public $players;

        public function __construct($whoseTurn){
            $this->whoseTurn = $whoseTurn;
            $this->players = array();
        }

        public function addPlayer($player){
            array_push($this->players, $player);
        }

        public static function mapFromJson($json){
            $obj = new Self($json->whoseTurn);
            $obj->players = $json->players;
            return $obj;
        }
    }
?>