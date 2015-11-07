<?php
@session_start();
class lc {
  public $sqli;
  
  public function __construct(){
    $this->sqli = new mysqli('localhost', 'sogo','sogo', 'fsdpaywj_sogo');
    $this->sqli->set_charset('utf8');
  }
  
  public function changepassword(){
    $result = $this->sqli->query("SELECT `name`, `key`, `password` FROM `users` WHERE `uid`='{$_SESSION['uid']}'") or die($this->sqli->error);
    $check = $result->fetch_assoc();
	
	
	if(isset($_POST['change'])) {
		$pass = array(
			"sql" => $check['password'],
			"old" => md5($check['key'].$_POST['old_pass']),
		);
		#print "POST: ".$pass['old']."<BR><BR> VVOD: ". $pass['sql'] ."<BR><BR> SESS: ".$_SESSION['name'];
		 if($pass['old'] != $pass['sql']) {
			print  "<div class='lr er'>Неверный старый пароль!</div>";
			return false;
		 } elseif($_POST['pass'] != $_POST['pass2']) {
			print  "<div class='lr er'>Новые пароли не совпадают!</div>";
			return false;
		 } elseif (strlen($_POST['pass'])<3 or strlen($_POST['pass'])>30) {
			print "<div class='lr er'>Пароль должен быть от 3 до 30 символов</div>"; 
		 } else {
			print  "<div class='lr er'>Хорошо! Вы сменили пароль!</div>";
			if(isset($_POST['pass']) and !empty($_POST['pass']) and !empty($_POST['pass2'])) {
				$tmp = $this->sqli->real_escape_string(trim($_POST['pass']));
				$tmp = md5($check['key'].$tmp);
				$sql = "UPDATE `users`
						SET `password`='{$tmp}'
						WHERE `uid`='{$_SESSION['uid']}'";
				$this->sqli->query($sql);
				return true;
			}
		 }
	}
  }
  
  public function buy(){
	$msg = null;
	
	if(empty($_SESSION['uid'])){
			$msg = "<div class='hint'> Пожалуйста авторизуйтесь для корректной работы системы! </div>";
			$bool = false;
	} else {
		if(isset($_POST['donate'])) {
			if(is_numeric($_POST['cash'])) {
				#code_intergrate system pay
				$msg = "Запрос выполняется. Пожалуйста подождите :)";
			} elseif(empty($_POST['cash'])) {
				$msg = "Пожалуйста введите сумму.";
			} else {
				$msg = "Вы можете ввести только целые числа.";
			}
		}
	}

		$flyPrice = 50;
		$balance = 49;
	
	if(isset($_POST['buyFly']) && isset($_SESSION['name'])){
		$flyAccess = "";
		//~ insert in perm
		$perm = array (
			1 => $_SESSION['name'],
			2 => $flyAccess,
			3 => $flyPrice,
		); 
		if($flyPrice <= $balance) {
			$balance = $balance - $flyPrice;
			$msg = "Вы успешно купили FLY!";
		} else {
			$msg = "Недостаточно средств на вашем аккаунте. Пожалуйста пополните свой баланс!";
		}
	}
	
	echo "
		<!-- <div class='hint'> Для того чтобы купить/пополнить счет нужно авторизироваться </div> -->
		 {$msg}
        <div class='profile'>
			<form action='' method='post'>
				<div class='lr er'><input class='ilc' type='text' name='cash' placeholder='100 рублей'></div>
				<div class='regI'><input type='submit' value='Пополнить счет' name='donate' /></div>
			</form>
			
			<a href='#mode=fly' ><div class='lr er about'> О FLY? </div></a>
			<form action='' method='post'>
				<div class='regI'><input type='submit' value='Купить FLY ({$flyPrice} руб.)' name='buyFly' /></div>
			</form>
		</div>
	";
  }
  
  public function __destruct(){
    $this->sqli->close();
  }
}
$lc = new lc();
?>