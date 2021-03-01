<?php 
    session_start();

    if(empty($_SESSION['id'])){
        header("location:components/login/login.php");
        exit();
    } else {
        header("location:components/menu/menu.php");
        exit();
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
    </head> 
    <body>
        <?php

        ?>
    </body>
</html>