<?php

class News {
  public static $mysqli;
  public $limit = 4;
  
  public function __construct() {
    self::$mysqli = new mysqli('localhost', 'sogo','sogo', 'fsdpaywj_sogo');
    self::$mysqli->set_charset('utf8');
  }
  
  public function page($get) {
    $_GET[$get] = NULL;
    $page  = NULL;
    $res   =  self::$mysqli->query("SELECT `id` FROM `news`");
    $pct   =  ceil($res->num_rows / $this->limit);  
    
    for($i = 0; $i < $pct; $i++ ) { 
        $page .= '<a href="?'.$get.'=' . ($i * $this->limit)  . '">' . ($i + 1)  . '</a>';
    } 
    
    if(is_numeric($_GET[$get]) != 0 || $res->num_rows > $this->limit )
        echo $page;

  }
  
  public function output() {
    $go    =  isset($_GET['page'])   ? intval( abs($_GET['page']) )   : 0 ;
    $go    =  self::$mysqli->real_escape_string($go);
    $res   =  self::$mysqli->query("SELECT `id`, `title`, `desc`, `date`, `img` FROM `news` ORDER BY `id` DESC LIMIT $go, $this->limit"); // 0, 4 = 4, 4 = 8, 4

    if($res->num_rows != 0) {
      while($row = $res->fetch_assoc()){
        $content = "
          <div class='news'>
          <a href='?feed=$row[id]'>
          <div class='newsh' style='background-image: url($row[img]);'></div>
          </a>
          <div class='newsc'>$row[title]</div>
          <div class='newsf'>$row[date]</div>
          </div>
		<!--  <dev class='post_tab'>
				<dev class='post_date'>$row[date]</dev>
				<dev class='post_info'>$row[desc]</dev>
		  </dev> -->
        ";
        echo $content;
      }
    }
  }
  
  public function full() {
    if(isset($_GET['feed']) and !empty($_GET['feed']) and is_numeric($_GET['feed'])){
      $id   = self::$mysqli->real_escape_string(htmlspecialchars($_GET['feed']));
      $res  = self::$mysqli->query("SELECT `title`, `desc`, `date` FROM `news` WHERE `id`='{$id}' LIMIT 1") or die("ERR");
      if($res->num_rows != 0)
        while($row = $res->fetch_assoc()){
          $content = "
          <div class='nwf'> 
            <div class='nwb'>
            <div class='nwt'>$row[title]</div>
            <div class='nwda'>$row[date]</div>
            </div>
            <div class='nwd'>$row[desc]</div>
          </div>
          <hr>
          ";
        }
      else
        header("Location: http://sogo.su/ ");
      echo $content;
    } else {
      header("Location: http://sogo.su/ ");
    }
  }
  
  function __destruct() {
        self::$mysqli->close();
  }
}
$news = new News();


?>