<?php
    session_start();

    require_once(__DIR__."/../../services/database-service.php");
    require_once(__DIR__."/../../models/db-game-room.php");   
    require_once __DIR__.'/../../services/language-service.php';
    $languageService = new LanguageService();

    $dbService = new DatabaseService();
    $gameRooms = $dbService->GetGameRooms();

    if ($_POST['gameRoomName'] != "") {
        $_POST['gameRoomName'] = str_replace(' ', '', $_POST['gameRoomName']);
        $showExistingRoomAlert = 0;
        $isGameRoomExist = 0;
        //sprawdzamy czy takiego pokoju nie ma

        foreach($gameRooms as $gameRoom){
            if($gameRoom->RoomName == $_POST['gameRoomName'])
                $isGameRoomExist = 1; 
        }  
    if($isGameRoomExist == 0){
        $gameRoom = new DbGameRoom();
        $gameRoom->RoomName = $_POST['gameRoomName'];
        $gameRoom->Player1Id = $_SESSION['id'];
        
        $dbService->InsertGameRoom($gameRoom);
        header('location:../game-room/game-room.php?room_name='.$gameRoom->RoomName.'');
        exit();
    }
        else{
            $showExistingRoomAlert = 1;
        }
    } 
    $user = $dbService->GetUserById($_SESSION['id']);

    echo '<div class="toolbar">';
    echo '<p>'.$languageService->language->logged_as.': '.$user->Nick.'</p>';
    echo '<p>'.$languageService->language->win.': '.$user->Wins.'</p>';
    echo '<p>'.$languageService->language->lost.': '.$user->Lost.' </p>';
    echo '<a id="logout" href="http://localhost:8000/components/logout/logout.php">'.$languageService->language->logout.'</a>';
    echo '<div class="languages">';
    echo '<img src="../../assets/polish.png" name="polish"> ';
    echo '<img src="../../assets/english.png" name="english">';
    echo '</div>';
    echo '<div class="font-size">'.$languageService->language->font_size. ':
     <p class="font-changer" name="smaller" id="font-changer-small">A </p>
     <p class="font-changer" name="bigger" id="font-changer-big"> A</p>';
    echo '</div>';
    echo '<div class="theme">'.$languageService->language->theme. ':
     <p class="theme-changer" name="bright" id="bright-theme">A</p>
     <p class="theme-changer" name="dark" id="dark-theme"> A</p>';
    echo '</div>';
    echo '</div>';

    echo '<div class="container">';
    if($showExistingRoomAlert == 1)
        echo '<p class="existing-room-alert">'.$languageService->language->name_exist.'</p>';

    require_once("menu-form.php"); 
    echo '<div class="rooms-container">';                
    if(empty($gameRooms))
        echo '<p class="no-rooms">'.$languageService->language->no_games.'</p>';
    else{
        foreach ($gameRooms as $gameRoom) {
            echo "<br><b>";
            echo $gameRoom->RoomName . "</b> ";
        if($gameRoom->Player2Id == NULL){
            echo "1/2 ". $languageService->language->players;
            echo '<a href="http://localhost:8000/components/game-room/game-room.php?room_name='.$gameRoom->RoomName.'"> '.$languageService->language->join.'</a>';
        } else {
            echo "2/2 ". $languageService->language->players;
        }
        echo "<br>";
        }
        echo "</div>";

    }
    echo "</div>";
?>