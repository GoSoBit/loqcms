<?php
include("../class.auth.php");
 if(!isset($_SESSION['name'])){
    $auth = new auth();
    if (isset($_REQUEST['reg'])) {
      if ($auth->reg()) {
        print '<div class="lr er">Регистрация успешна на ваш e-mail придет письмо. <a href="/">Вы можете войти в систему -></a>.</div>';
         /* ~~~ */
        $auth->successful_reg($_REQUEST['mail'], $_REQUEST['_login']);
        /* ~~~ */
      } else {
        print '<div class="lr er">'.$auth->error_reporting().'</div>';
      }
    } else return false;
  } else { echo "Вы уже зарегистрированы!";} 
?>