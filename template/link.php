<?php
switch(@$_GET['mode']) {
	case "recovery": 
		include('template/recovery.html');
		break;
	case "registration": 
		echo '
			<div class="hint">Регистрируясь на сайте вы автоматичеки соглашаетесь с правилами!</div>
			<div id="regiMsg"></div>
		';
		include('template/registration.html');
		break;
	case "changepassword": 
		if(isset($_SESSION['uid']))
		include('template/changepassword.html');
		else
		echo "Чтобы войти в профиль нужно авторизироваться!";
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
		else $news->full(); 
} 
?>