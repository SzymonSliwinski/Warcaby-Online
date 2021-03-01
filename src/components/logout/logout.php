<?php
    header("Location: http://localhost:8000/index.php");

    session_start();

    unset($_SESSION['id']);

    session_destroy();
    
