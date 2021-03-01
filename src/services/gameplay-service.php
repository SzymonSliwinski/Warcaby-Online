<?php
    session_start();
    require_once 'file-service.php';

    class GameplayService{
        private $fields;
        private $fileService;
        function __construct(){
            $this->InitializeStartingFields();
            $this->fileService = new FileService();
        }

        private function InitializeStartingFields(){
            $this->fields = array(
                array('e', 'w', 'e', 'w', 'e', 'w', 'e', 'w'),
                array('w', 'e', 'w', 'e', 'w', 'e', 'w', 'e'),
                array('e', 'w', 'e', 'w', 'e', 'w', 'e', 'w'),
                array('e', 'e', 'e', 'e', 'e', 'e', 'e', 'e'),
                array('e', 'e', 'e', 'e', 'e', 'e', 'e', 'e'),
                array('b', 'e', 'b', 'e', 'b', 'e', 'b', 'e'),
                array('e', 'b', 'e', 'b', 'e', 'b', 'e', 'b'),
                array('b', 'e', 'b', 'e', 'b', 'e', 'b', 'e')
            );
        }

        public function GetFields(){
            return $this->fields;
        }

        public function MakeMove($roomName, $move){
            $actualFields = $this->fileService->ConvertTxtFieldsToArray($roomName);
            $pickedFieldValue = $actualFields[$move->oldId];
            $actualFields[$move->oldId] = 'e';

            if($pickedFieldValue == 'w1' || $pickedFieldValue == 'b1' || $move->changeOnQueen == 0){
                $actualFields[$move->newId] = $pickedFieldValue;
            } else if($move->changeOnQueen == 1) {
                $actualFields[$move->newId] = $pickedFieldValue.'1';
            } 

            $this->fileService->CreateFile($roomName, ".txt", $this->fileService->ConvertFieldsToString($actualFields));
            $this->fileService->CreateFile($roomName, ".html", $this->fileService->GetHtmlBoard(
                $this->fileService->ConvertTxtFieldsToArray($roomName),
                $this->fileService->GetGameState($roomName)
            ));        }

        public function ChangeTurn($roomName){
            $actualState = $this->fileService->GetGameState($roomName);
            $actualState->whoseTurn = $this->GetNewTurnColor($actualState->whoseTurn);
            $this->fileService->CreateFile($roomName, ".json", json_encode($actualState));
        }

        public function RemoveChecker($roomName, $id){
            $actualFields = $this->fileService->ConvertTxtFieldsToArray($roomName);
            $actualFields[$id] = 'e';
            $gameState = $this->fileService->GetGameState($roomName);
            print_r($gameState);
            if($gameState->players[0]->id == $_SESSION['id']){
               $gameState->players[1]->checkersLeft--; 
            } else{
                $gameState->players[0]->checkersLeft--; 
            }
            $this->fileService->CreateFile($roomName, ".json", json_encode($gameState));
            $this->fileService->CreateFile($roomName, ".txt", $this->fileService->ConvertFieldsToString($actualFields));
            $this->fileService->CreateFile($roomName, ".html", $this->fileService->GetHtmlBoard(
                $this->fileService->ConvertTxtFieldsToArray($roomName),
                $gameState
            ));
        }

        private function GetNewTurnColor($color){
            if($color == 'w')
                return 'b';
            else 
                return 'w';
        }
    }
?>