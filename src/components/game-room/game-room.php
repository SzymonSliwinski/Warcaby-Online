<?php session_start(); ?>
<html>

<head>
    <meta charset="utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="script.js" async></script>
    <title>Warcaby</title>
    <link rel="stylesheet" href="css/board.css">
    <style>
        <?php
            include 'css/'.$_COOKIE['font'].'.css'; 
            include 'css/'.$_COOKIE['theme'].'.css'; 
        ?>
    </style>
</head>
    <?php
        require_once __DIR__.'/../../services/players-service.php';
        require_once __DIR__.'/../../services/file-service.php';
        require_once __DIR__.'/../../services/gameplay-service.php';

        $roomName = $_GET['room_name'];
        $fileService = new FileService();
        $playersService = new PlayersService($roomName, $fileService);

        if($fileService->IsGameFilesExist($_GET['room_name']) == false){
            $gameplayService = new GameplayService();
            $playersService->JoinFirstPlayer();
            $fields = $gameplayService->GetFields();
            $fileService->InitializeBoardFiles($_GET['room_name'], $fields);
        }

        $actualGameRoomState = GameRoomState::mapFromJson($fileService->GetGameState($roomName));

        if($actualGameRoomState->players[0]->id != $_SESSION['id'] && !$actualGameRoomState->players[1]){
            $playersService->JoinSecondPlayer($actualGameRoomState);
        }

        if($actualGameRoomState->players[0]->id != $_SESSION['id'] && $actualGameRoomState->players[1]->id != $_SESSION['id'] ){
            echo '<script type="text/javascript">';
            echo 'alert("Dostęp zabroniony!");';
            echo '</script>';        
            exit();
        }
    ?>
<body>
    <div id="container">

    </div> 
</body>
</html>