<?
if(!isset($_SESSION['name'])){
  include("../class.auth.php");
  $auth = new auth();
  $r='';
  if (isset($_REQUEST['rec'])) {
    if ($auth->recovery_pass($_REQUEST['_login'], $_REQUEST['mail'])) {
      print '<div  class="lr er">Новый пароль был отправлен на Ваш почтовый ящик. <a href="/index.php">Войти.</a></div>';
    } else {
      print '<div  class="lr er">'.$auth->error_reporting().'</div>';
    }
  } 
} else echo "Вы в системе!";
?>