<?php
switch(@$_GET['mode']) {
	case "recovery": 
		include('template/recovery.html');
		break;
	case "registration": 
		include('template/registration.html');
		break;
	case "profile": 
		$lc->profile();
		break;
	case "donate": 
		$lc->buy();
		break;
	case "forum": 
		echo "Страница находится в разработке";
		break;
	default: 
		if(!isset($_GET['feed'])){ 
			$news->output(); 
			echo "<div class='nwbs'></div><div class='page'> ";
			$news->page('page');  
			echo "</div>"; 
		}
		elseif(isset($_GET['lc'])) $lc->profile();
		else $news->full(); 
}  
 /*  if(isset($_GET['recovery'])) include('recovery.html');
   elseif(isset($_GET['reg'])) include('registration.html');
   elseif(isset($_GET['lc'])) $lc->profile();
   elseif(isset($_GET['buy'])) $lc->buy();
   elseif(isset($_GET['forum'])) echo "Страница находится в разработке";
   elseif(isset($_GET['service'])) echo "Страница находится в разработке";
else {
  if(!isset($_GET['feed'])){ 
    $news->output(); 
	echo "<div class='nwbs'></div><div class='page'> ";
	$news->page('page');  
	echo "</div>"; 
  }
  else { $news->full(); }
}*/
?>