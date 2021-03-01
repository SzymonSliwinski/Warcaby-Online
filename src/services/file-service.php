<?php
    require_once __DIR__.'/../models/player.php';
    require_once __DIR__.'/../models/game-room-state.php';

    class FileService{
        private $path;
        
        function __construct(){
            $this->path = __DIR__."/../game-state-files/";
        }

        function IsGameFilesExist($fileName){
            if(file_exists($this->path.$fileName.".html")
                && file_exists($this->path.$fileName.".json")
                && file_exists($this->path.$fileName.".txt")){
                return true;
            } else {
                return false;
            }
        }

        function CreateFile($fileName, $extension, $content){
            $file = fopen($this->path.$fileName.$extension, "w");
            fwrite($file, $content);
            fclose($file);
        }

        function GetActualBoardFromFile($fileName){
            return file_get_contents($this->path.$fileName.".html");
        }

        function GetGameState($fileName){
            return json_decode(file_get_contents($this->path.$fileName.".json"));
        }

        public function InitializeBoardFiles($roomName, $fields){
            $this->CreateFile($roomName, ".txt", $this->ConvertStartingFieldsToString($fields));
            $this->CreateFile($roomName, ".html", $this->GetHtmlBoard($this->ConvertTxtFieldsToArray($roomName), $this->GetGameState($roomName)));
        }

        //przerabia startowa tablice 2d na string
        private function ConvertStartingFieldsToString($fields){
            $result;

            for($row= 0; $row < 8; $row++)
            { 
                for($col= 0; $col < 8; $col++){
                    $result .= $fields[$row][$col].'---';
                }
            }
            return $result; 
        }

        //przerabia generowane w trakcie gry tablice 1d na string
        public function ConvertFieldsToString($fields){
            $result;
            
            for($i= 0; $i < 64; $i++){
                $result .= $fields[$i].'---';
            }
            
            return $result; 
        }

        public function GetLanguage($lang){
            return json_decode(file_get_contents(__DIR__.'/../languages/'.$lang.".json"));
        }

        public function ConvertTxtFieldsToArray($fileName){
            $txtFields = file_get_contents($this->path.$fileName.".txt");
            $result = explode("---", $txtFields);
            return $result;
        }

        public function DeleteGameFiles($fileName){
            unlink($this->path.$fileName.".json");
            unlink($this->path.$fileName.".html");
            unlink($this->path.$fileName.".txt");
        }

        public function GetHtmlBoard($fields, $gameState){
            $result = '';  
            
            $result .= '<div class="player-container">';
            $result .= '<div class="white-player-field">'. $gameState->players[0]->nick.'</div>';
            if($gameState->players[1])
                $result .= '<div class="black-player-field">'. $gameState->players[1]->nick .'</div>';
            else
                $result .= '<div class="black-player-field">??????????</div>';
            $result .= '</div>';
            $result .= '<div id="#board-container">';
            $result .= '<table id="board">';
            $i = 0;

            for($row= 0; $row < 8; $row++)
            {
                $result .= '<tr>';
 
                for($col= 0; $col < 8; $col++){            
                    if($row % 2 == $col % 2){
                        $class = "white";
                    }
                    else
                    {
                        $class = "black";
                    }
                    
                    if($fields[$i] == 'b')
                    {
                        $result .= '<td class="black black-checker '.$class.'" id="'.$i.'">'. $fields[$i].'</td>';
                    }
                    else if($fields[$i] == 'w')   {
                        $result .= '<td class="white-checker '.$class.'" id="'.$i.'">'. $fields[$i] .'</td>';

                    } else if($fields[$i] == 'w1'){
                        $result .= '<td class="white-queen-checker '.$class.'" id="'.$i.'">'. $fields[$i] .'</td>';
                    }
                    else if($fields[$i] == 'b1'){
                        $result .= '<td class="black-queen-checker '.$class.'" id="'.$i.'">'. $fields[$i] .'</td>';
                    }
                    else
                    {
                        $result .= '<td class="'.$class.'" id="'.$i.'"></td>';
                    }
                    $i++;
                    
                }
                $result .= '</tr>';
            }
            $result .= '</table>'; 
            $result .= '</div>';

            return $result; 
        }
    }
?>