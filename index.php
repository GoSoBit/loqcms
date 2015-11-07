<?php
error_reporting(E_ALL); ini_set('display_errors', 1); 
?>
<html>
<head>
  <meta charset='utf-8'/>
  <title>LOQ</title>
<link rel="shortcut icon" href="/favicon.gif" type="image/x-icon">
<link rel="icon" href="/favicon.gif" type="image/x-icon">
  <link rel="stylesheet" href="template/style.css" />
  <link href='http://fonts.googleapis.com/css?family=Roboto:300&subset=latin,cyrillic-ext,cyrillic,greek-ext,greek,vietnamese,latin-ext' rel='stylesheet' type='text/css'>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  <script type="text/javascript" src="mods/ajax.js"></script>
</head>
<?php
include('mods/class.news.php');
include('mods/class.auth.php');
include('mods/class.lc.php');
?>
<body>
<warrper>
  <header>
    <menu>
      <nav><a href='?mode=forum'>ФОРУМ</a></nav>
      <nav class='sogo transition-scale'>
        <a href='/'><img width='70%' height='165%' src='template/img/logo.png' /></a>
      </nav>
      <nav><a href='?mode=donate'>УСЛУГИ</a></nav>
    </menu>
  </header>
  <content>
    <div class='block'>
      <article>
        <error id="msg"></error>
        <div id="content"></div>
          <? include('template/link.php'); ?>
      </article>
      <aside >
          <? include('template/side.html'); ?>
      </aside>
    </div>
    <footer>
       <? include('template/footer.html'); ?>
    </footer>
  </content>
  <footer>
    <a href='http://vk.com/sogo_su'>OUR GROUP</a>
    &nbsp;&nbsp;dev - theGoys
  </footer>
</warrper>
</body>
</html>