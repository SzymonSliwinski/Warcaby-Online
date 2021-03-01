<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo $languageService->language->login; ?></title>
  <link rel="stylesheet" href="login.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="login.js"></script>
</head>

<body>
  <div class="container" id="formContainer">
    <h2><?php echo $languageService->language->login; ?></h2>
    <form action="login.php" method="post">
      <input type="text" placeholder="E-mail" name="email" />
      <br />
      <input type="password" placeholder=<?php echo '"'.$languageService->language->password.'"'; ?> name="password" />
      <br />
      <div class="row">
        <button type="submit"><?php echo $languageService->language->login; ?></button>
      </div>
    </form>
    <?
      if($showError == 1){
          echo '<p id="failed">'. $languageService->language->data_error .'</p>';
      }
    ?>
    <p><?php echo $languageService->language->no_account; ?> <a href="http://localhost:8000/components/signup/signup.php"><?php echo $languageService->language->sign_up; ?></a></p>
  </div>
</body>

</html>