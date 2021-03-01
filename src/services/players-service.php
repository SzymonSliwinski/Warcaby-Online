<?php
    session_start();
    require_once 'database-service.php';
    require_once __DIR__.'/../models/player.php';
    require_once __DIR__.'/../models/game-room-state.php';

    class PlayersService{
        private $gameRoom;
        private $fileService;
        private $dbService;
        public function __construct($roomName, $fileService){
            $this->fileService = $fileService;
            $this->dbService = new DatabaseService();
            $this->gameRoom = $this->dbService->GetGameRoomByName($roomName);
        }

        public function JoinFirstPlayer(){
            $player = new Player(
                $_SESSION['id'],
                $this->dbService->GetPlayerNickFromGameRoomById($_SESSION['id'], "1"),
                'w'
            );
            $gameRoomState = new GameRoomState('w');
            $gameRoomState->addPlayer($player);
            
            $this->fileService->CreateFile($this->gameRoom->RoomName, ".json",  json_encode($gameRoomState));
        }

        public function JoinSecondPlayer($gameRoomState){
            $this->dbService->SetSecondPlayerInGameRoom($this->gameRoom->RoomName, $_SESSION['id']);
            
            $player = new Player(
                $_SESSION['id'],
                $this->dbService->GetPlayerNickFromGameRoomById($_SESSION['id'], "2"),
                'b'
            );

            $gameRoomState->addPlayer($player);
            $this->fileService->CreateFile($this->gameRoom->RoomName, ".json", json_encode($gameRoomState));
        }
    }
?>