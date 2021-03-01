<?
    require_once __DIR__.'/../../services/database-service.php';
    require_once __DIR__.'/../../services/language-service.php';
    $languageService = new LanguageService();
    $showError = 0;

    if ((isset($_POST['email']) && isset($_POST['password'])) && $_POST['email'] != "" && $_POST['password'] != "") {
        $dbService = new DatabaseService();
        $dbUser = $dbService->GetUserDataByEmail($_POST['email']);
  
        if($dbUser){
            $showError = 0;

            session_start();
            if(password_verify($_POST['password'], $dbUser->Password )){
                session_start();
                $_SESSION['id'] = $dbUser->Id;
                header('Location: http://localhost:8000/index.php');
                exit();
            } else {
                $showError = 1;
            }
        } else {
            $showError = 1;
        }
    } else if((isset($_POST['email']) && isset($_POST['password'])) && ($_POST['email'] == "" || $_POST['password'] == "")) {
        $showError = 1;
    }

    require 'login-form.php';   

?>
