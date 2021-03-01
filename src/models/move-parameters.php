<?php
  
    class MoveParameters{
        public $oldId;
        public $newId;
        public $changeOnQueen;
        
        public function __construct($oldId, $newId, $changeOnQueen){
            $this->oldId = $oldId;
            $this->newId = $newId;
            $this->changeOnQueen = $changeOnQueen;
        }
    }
?>