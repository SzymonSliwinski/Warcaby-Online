<?php
    require_once("../../services/database-service.php");
    require_once("../../models/user.php");
    require_once __DIR__.'/../../services/language-service.php';
    
    $languageService = new LanguageService();
    $showEmptyFields = 0;
    
    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['nick']) && $_POST['nick'] != "" && $_POST['password'] != "" && $_POST['email'] != "") {
        $dbService = new DatabaseService();
        $user = new User();

        $user->Password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $user->Nick = trim(($_POST['nick']));
        $user->Email = trim($_POST['email']);
        $dbService->InsertUsers($user);
        
        header("Location: http://localhost:8000/components/login/login.php");
        exit;
    }
    else if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['nick'])){
        $showEmptyFields = 1;
    }
    require 'signup-form.php';   

?>