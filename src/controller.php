<?php 
    session_start(); 

    require_once __DIR__.'/services/file-service.php';
    require_once __DIR__.'/services/gameplay-service.php';
    require_once __DIR__.'/services/language-service.php';
    require_once __DIR__.'/models/move-parameters.php';
    require_once __DIR__.'/services/database-service.php';


    $fileService = new FileService();
    $gameplayService = new GameplayService();
    $databaseService = new DatabaseService();

    if($_GET['is_second_player_joined']){
        $roomName = $_GET['room_name'];

        if($fileService->GetGameState($roomName)->players[1] == NULL){
            echo 0;
        } else {
            echo 1;
        }
    }
    else if($_GET['get_color']){
        $players = $fileService->GetGameState($_GET['room_name'])->players;
        foreach ($players as $player)
            if($player->id == $_SESSION['id'])
                echo $player->color;
    }
    else if($_GET['get_turn']){
        $roomState = $fileService->GetGameState($_GET['room_name']);
        echo $roomState->whoseTurn;
    }
    else if($_GET['make_move']){
        $move = new MoveParameters($_POST['oldId'], $_POST['newId'], $_POST['changeOnQueen']);
        $gameplayService->MakeMove($_POST['roomName'], $move);

        if($_POST['isRemove'] == 0){
            $gameplayService->ChangeTurn($_POST['roomName']);
            $roomState = $fileService->GetGameState($_POST['roomName']);
            echo $roomState->whoseTurn; 
        } else {  
            $gameplayService->RemoveChecker($_POST['roomName'], $_POST['capturedCheckerId']);
            $roomState = $fileService->GetGameState($_POST['roomName']);
            echo $roomState->whoseTurn; 
        }
    }
    else if($_GET['change_turn']){
        $gameplayService->ChangeTurn($_GET['room_name']);
        $roomState = $fileService->GetGameState($_GET['room_name']);
        echo $roomState->whoseTurn; 
    }
    else if($_GET['update_player']){
        $fileService->CreateFile($_GET['room_name'], ".html", $fileService->GetHtmlBoard(
            $fileService->ConvertTxtFieldsToArray($_GET['room_name']),
            $fileService->GetGameState($_GET['room_name'])
        ));
    }
    else if($_GET['set_cookie']){
        $cookie_name = 'language';
        $cookie_value = $_GET['value'];
        setcookie($cookie_name, $cookie_value, time() + (6*30*24*3600), "/");
    }
    else if($_GET['set_cookie_font']){
        $cookie_name = 'font';
        $cookie_value = $_GET['value'];
        setcookie($cookie_name, $cookie_value, time() + (6*30*24*3600), "/");
    }
    else if($_GET['set_cookie_theme']){
        $cookie_name = 'theme';
        $cookie_value = $_GET['value'];
        setcookie($cookie_name, $cookie_value, time() + (6*30*24*3600), "/");
    }
    else if($_GET['check_is_win']){
        $gameState = $fileService->GetGameState($_GET['room_name']);
        $winnerId;
        if($gameState->players[0]->checkersLeft == 0)
        {
            $winnerId = $gameState->players[1]->id;
            $databaseService->UpdateUserLosts($gameState->players[0]->id);
            $databaseService->UpdateUserWins($winnerId);
            echo $gameState->players[1]->nick;
        } 
        else if($gameState->players[1] && $gameState->players[1]->checkersLeft == 0)
        { 
            $winnerId = $gameState->players[0]->id;
            $databaseService->UpdateUserLosts($gameState->players[1]->id);
            $databaseService->UpdateUserWins($winnerId);
            echo $gameState->players[0]->nick;
        }
        else{ 
            echo '0';  
        }

    }
    else if($_GET['delete_game_room_data']){  
        $fileService->DeleteGameFiles($_GET['room_name']);   
        $databaseService->DeleteGameRoom($_GET['room_name']);
    }
    else if($_GET['get_language']){
        $langService = new LanguageService();

        echo json_encode($langService->language);
    }
?>