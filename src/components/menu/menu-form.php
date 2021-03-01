<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="stylesheet" href="css/menu.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="menu.js"></script>
    <style>
    <?php
        include 'css/'.$_COOKIE['font'].'.css'; 
        include 'css/'.$_COOKIE['theme'].'.css'; 
    ?>
   </style>  
</head>
<body>
    <form method="post">
        <input id="game-room-name" type="text" placeholder=<?php echo '"'.$languageService->language->game_name.'"'; ?> name="gameRoomName" /></br>
        <button id="add-room-button" type="submit"><?php echo $languageService->language->create ?></button>
    </form>
</body>

</html>