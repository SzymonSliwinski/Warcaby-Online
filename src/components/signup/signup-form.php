<html>

<head>
  <meta charset="UTF-8">
  <title><?php echo $languageService->language->sign_up; ?></title>
  <link rel="stylesheet" href="signup.css">

</head>

<body>
  <div class="container">
    <h2><?php echo $languageService->language->sign_up; ?></h2>
    <form action="signup.php" method="post">
      <input type="text" placeholder=<?php echo '"'.$languageService->language->name.'"'; ?> name="nick" />
      <br />
      <input type="text" placeholder="E-mail" name="email" />
      <br />
      <input type="password" placeholder=<?php echo '"'.$languageService->language->password.'"'; ?> name="password" />
      <br />
      <button type="submit" name="Submit"><?php echo $languageService->language->sign_up; ?></button>
    </form>
    <? 
      if($showEmptyFields == 1)
        echo '<p id="failed">'. $languageService->language->data_error .'</p>';
    ?>
  </div>
</body>

</html>