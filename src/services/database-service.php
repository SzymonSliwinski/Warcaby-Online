<?php
    require_once(__DIR__."/../models/user.php");
    require_once(__DIR__."/../models/db-game-room.php");

    class DatabaseService{
        private $connection;
        
        public function __construct(){
            $this->connection = new mysqli("mysql", "root", 'root', "Warcaby");
        }

        public function GetGameRooms(){
            $sql = "SELECT * FROM GameRooms ORDER BY RoomName;";
            $result = $this->connection->query($sql);
            $gameRooms = [];

            while ($data = $result->fetch_object()) {
                $gameRoom = new DbGameRoom();
                $gameRoom->Id = $data->Id;
                $gameRoom->RoomName = $data->RoomName;
                $gameRoom->Player1Id = $data->Player1Id;
                $gameRoom->Player2Id = $data->Player2Id;

                array_push($gameRooms, $gameRoom );
            }
            return $gameRooms;
        }

        public function GetGameRoomByName($gameRoomName){
            $sql = "SELECT * FROM GameRooms where RoomName='$gameRoomName';";
            $result = $this->connection->query($sql);
            $gameRoom = new DbGameRoom();
            
            while ($data = $result->fetch_object()) {
                $gameRoom->Id = $data->Id;
                $gameRoom->RoomName = $data->RoomName;
                $gameRoom->Player1Id = $data->Player1Id;
                $gameRoom->Player2Id = $data->Player2Id;
            }
            return $gameRoom;
        }

        public function GetUserById($id){
            $sql = "SELECT * FROM Users where Id='$id';";
            $result = $this->connection->query($sql);
            $user;
            
            while ($data = $result->fetch_object()) {
                $user = $data;
            }
            return $user;
        }

        public function InsertUsers($User){
            $sql = 'INSERT INTO Users (Email, Nick, Password, Wins, Lost) 
                VALUES("'.$User->Email.'", "'.$User->Nick.'", "'.$User->Password.'", 0, 0);';
            $this->connection->query($sql);
            $this->connection->close();
        }

        public function GetUserDataByEmail($email){
            $sql = "SELECT * FROM Users WHERE Email = '$email'";
            $result = $this->connection->query($sql);
            $user = new User();
   
            if (mysqli_num_rows($result) > 0){
                while ($data = $result->fetch_object()) {
                    $user->Id = $data->Id;
                    $user->Nick = $data->Nick;
                    $user->Email = $data->Email;
                    $user->Password = $data->Password;
                }
                return $user;
            }
            else
                return NULL;
        }

        public function InsertGameRoom($gameRoom){
            $sql = 'INSERT INTO GameRooms(RoomName, Player1Id) 
                VALUES ("'.$gameRoom->RoomName.'", "'.$gameRoom->Player1Id.'");';
            $this->connection->query($sql);
            $this->connection->close();
        }

        public function UpdateUserWins($userId){
            $sql = 'UPDATE Users SET Wins = Wins + 1 WHERE Id = "'.$userId.'";';
            $this->connection->query($sql);  
        }

        public function UpdateUserLosts($userId){
            $sql = 'UPDATE Users SET Lost = Lost + 1 WHERE Id = "'.$userId.'";';
            $this->connection->query($sql);  
        }

        public function DeleteGameRoom($gameRoomName){
            $sql = 'DELETE from GameRooms WHERE RoomName = "'.$gameRoomName.'";';
            $this->connection->query($sql);  

        }

        public function SetSecondPlayerInGameRoom($gameRoomName, $playerId){
           $sql = 'UPDATE GameRooms SET Player2Id = '.$playerId.' WHERE RoomName = "'.$gameRoomName.'";';
           $this->connection->query($sql);  
        }

        public function GetPlayerNickFromGameRoomById($playerId, $playerPosition){
            $sql = 'SELECT Users.Nick FROM GameRooms INNER JOIN Users 
                ON GameRooms.Player'.$playerPosition.'Id = Users.Id WHERE Users.Id = '.$playerId.';';
            $result = $this->connection->query($sql);
            $data = $result->fetch_object();
            return $data->Nick;
        }       
    }
?>