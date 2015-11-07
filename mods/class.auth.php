<?php
@session_start();
class auth { 
  public static $sqli;
  
  public function __construct(){
    self::$sqli = new mysqli('localhost', 'sogo','sogo', 'fsdpaywj_sogo');
    self::$sqli->set_charset('utf8');
  }
  
  /**
	*	MySQL data screening
	*
	*	@param	$data			Screening string
	*	@return	string		Result string
	*/
	public static function screening($data) {
		$data = trim($data);
		return self::$sqli->real_escape_string($data); //mysqli::escape_string ( string $escapestr )
	}

	/**
	 * MySQL data screening array
	 * @papam $data		screening array
	 * @return array	result array
	 */
	public static function screening_array($data) {
		foreach ($data as $key=>$value) {
			$tmp[$key]=self::screening($value); //self::screening, mysqli_real_escape
		}
		return $tmp;
	}
  
  public static $error_arr=array();
	public static $error='';
	
	/**
	 * This method validate user data
	 * @param		$login			user login
	 * @param		$passwd			user password one
	 * @param		$passwd2		user password two
	 * @param		$mail				user email
	 * @return	bollean			return true or false
	 */
	function check_new_user($login, $passwd, $passwd2, $mail) { 
		$taps = 15;
		$i = 0;
		$i = $_COOKIE['cooldown'] + 1;
		if($i <= $taps){
			setcookie('cooldown', $i, time()+900);
			setcookie('timer_cheker', idate("i"), time()+900);
		}
		if($i >= $taps){
			$sum = $_COOKIE['timer_cheker'] + 15;
			$timer = $sum - idate("i");
			$error[] = "Вы использовали слишком много попыток. Вы сможете повторить запрос через {$timer} минут! ";	
		}
		if (isset($error)) {
			self::$error_arr=$error;
			return false;
		}
		
		$resL = self::$sqli->query("SELECT * FROM `users` WHERE `name`='{$login}'") or die("ERR");
		$resM = self::$sqli->query("SELECT * FROM `users` WHERE `mail`='{$mail}'") or die("ERR");
		//~ validate user data
		if (empty($login) or empty($passwd) or empty($passwd2)) $error[]='Все поля обязательно нужно заполнить';
		if ($passwd != $passwd2) $error[]='Пароли не совпадают';
		if (strlen($login)<3 or strlen($login)>30) $error[]='Логин должен быть от 3 до 30 символов';
		if (strlen($passwd)<3 or strlen($passwd)>30) $error[]='Пароль должен быть от 3 до 30 символов';
		//~ validate email
		if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) $error[]='Не правильный email';
		//~ Checks the user with the same name in the database
		if ($resL->num_rows != 0) $error[]='Пользователь с таким именем уже существует';
		if ($resM->num_rows != 0) $error[]='Пользователь с таким Email уже существует';

		//~ return error array or TRUE
		if (isset($error)) {
			self::$error_arr=$error;
			return false;
		} else {
			return true;
		}
	}

  /**
   *	This method is used to register a new user
   *	@return	boolean or string			return true or html code error
   */
  function reg() {
    //~ screening input data
    $tmp_arr=$this->screening_array($_REQUEST);
    $login=$tmp_arr['_login'];
    $passwd=$tmp_arr['_passwd'];
    $passwd2=$tmp_arr['passwd2'];
    $mail=$tmp_arr['mail'];
    //~ User floor translate to a numeric value

    //~ Check valid user data
    if ($this->check_new_user($login, $passwd, $passwd2, $mail)) {
     //~ User data is correct. Register. 
      $user_key = $this->generateCode(10);
      $passwd = md5($user_key.$passwd); //~ password hash with the private key and user key
     // echo $user_key."<Br>".$passwd;
      $query = self::$sqli->query("INSERT INTO `users` (`name`, `password`, `mail`, `key`) VALUES ('".$login."', '".$passwd."', '".$mail."', '".$user_key."')");
      if ($query) {
        return true;
      } else {
        self::$error='Произошла ошибка при регистрации нового пользователя. Cообщите администрацией.';
        return false;
      }
    } else {
      return false;
    }
  }
  
  /**
	 * This method checks whether the user is authorized
	 * @return		boolean				true or false
	 */
	function check() { 
		if (isset($_SESSION['uid']) and isset($_SESSION['name'])) { 
			return true;
		} else { 
			//~ Verify the existence of cookies
			if (isset($_COOKIE['uid']) and isset($_COOKIE['code_user'])) { print false;
				//~ cookies exist. Verified with a table sessions.
				$uid   	   = $this->screening($_COOKIE['uid']);
				$code_user = $this->screening($_COOKIE['code_user']);
				$query     = self::$sqli->query("SELECT `session`.*, `users`.`name` FROM `session` INNER JOIN `users` ON `users`.`uid`=`session`.`uid` WHERE `session`.`uid`='{$uid}'") or die("ERR");
				if ($query and $query->num_rows != 0) { 
					//~ Cookies are found in the database
					$user_agent=$this->screening($_SERVER['HTTP_USER_AGENT']);
					while ($row = $query->fetch_assoc()) {
						if ($row['code_sess']==$code_user and $row['user_agent_sess']==$user_agent) {
              //session_start(); 
							//~ found record
							self::$sqli->query("UPDATE `session` SET `used_sess` = `used_sess`+1 WHERE `sid` = '{$row['sid']}'") or die(self::$sqli->error);
							//~ start session and update cookie
							$_SESSION['uid']=$row['uid'];
							$_SESSION['name']=$row['name'];
							setcookie("uid", $row['uid'], time()+3600*24*30);
							setcookie("code_user", $row['code_sess'], time()+3600*24*30);
							return true;
						}
					}
					//~ No records with this pair of matching cookies/user agent
					$this->destroy_cookie();
					return false;
				} else {
					//~ No records for this user
					$this->destroy_cookie();
					return false;
				}
			} else {
				//~ cookies nit exist
				$this->destroy_cookie();
				return false;
			}
		}
	}
  
  /**
	 * This method performs user authorization
	 * @return boolen			true or false
	 */
	function authorization() {
		$taps = 15;
		$i = 0;
		if(isset($_COOKIE['cooldown_auth']))
			$i = $_COOKIE['cooldown_auth'] + 1;
		if($i <= $taps){
			setcookie('cooldown_auth', $i, time()+900);
			setcookie('timer', @idate("i"), time()+900);
		}
		
		if($i >= $taps){
			$sum = $_COOKIE['timer'] + 15;
			$timer = $sum - @idate("i");
			self::$error = "Вы использовали слишком много попыток. Вы сможете повторить запрос через {$timer} минут! ";	
			return false;
		}

	
		//~ screening user data
		$user_data = $this->screening_array($_REQUEST);
		//~ Find a user with the same name and taking his key
		$result    = self::$sqli->query("SELECT `key`, `uid`, `name`, `password` FROM `users` WHERE `name`='{$user_data['login']}'") or die(self::$sqli->error);
		$find_user = $result->fetch_assoc();
		$ses       = self::$sqli->query("SELECT `uid` FROM `session` WHERE `uid`='{$find_user['uid']}'") or die(self::$sqli->error);
	if (!$find_user) {
			//~ user not found
			self::$error='Пользователя не существует <a href="?mode=recovery">восстановить пароль</a>';
			return false;
	} else {
			//~ user found
			$passwd= md5($find_user['key'].$user_data['passwd']); //~ password hash with the private key and user key
			if ($passwd==$find_user['password']) {
				//~ passwords match
				$_SESSION['uid']    =  $find_user['uid'];
				$_SESSION['name'] =  $find_user['name'];
				$cook_code=$this->generateCode(15);
				$user_agent=$this->screening($_SERVER['HTTP_USER_AGENT']);
				$user_ip=$this->screening($_SERVER['REMOTE_ADDR']);
				if($ses->num_rows < 1){				
					self::$sqli->query("INSERT INTO `session` ( `uid`, `used_sess`, `code_sess`, `user_agent_sess`, `ip`) VALUES ('".$find_user['uid']."', 1, '".$cook_code."', '".$user_agent."', '".$user_ip."')") 
					or die(self::$sqli->error);
				} else { // no dupe stlb!
					self::$sqli->query("UPDATE `session` SET `code_sess`='".$cook_code."', `user_agent_sess`='".$user_agent."', `used_sess`=used_sess+1, `ip`='".$user_ip."' WHERE `uid` = ".$find_user['uid']."");
				}	//~~~~
					setcookie("uid", $_SESSION['uid'], time()+3600*24*30);
					setcookie("code_user", $cook_code, time()+3600*24*30);
				/* if(isset($_COOKIE['uid'], $_COOKIE['code_user'])) */
								   print "<script>window.location = 'http://".$_SERVER['HTTP_HOST']."';</script>";
			} else {
				//~ passwords not match
				self::$error='Пользователь не найден или пароль неверный';
				return false;
			}
		}
	}
  
  
	/**
	 * This method is used for the user exit
	 */
	function exit_user() {
		//~ Destroy session, delete cookie and redirect to main page
   /*  unset($_SESSION['name']);
    unset($_SESSION['uid']); */
    session_destroy();
		setcookie("uid", '', time()-3600);
		setcookie("code_user", '', time()-3600);
    unset($_COOKIE["uid"]); 
    unset($_COOKIE["code_user"]);
		header("Location: index.php");
	}

	/**
	 * This method destroy cookie
	 */
	function destroy_cookie() {
		setcookie("uid", '', time()-3600);
		setcookie("code_user", '', time()-3600);
	}

	/**
	 * This method is used for password recovery.
	 */
	function recovery_pass($login, $mail) {
		$login=$this->screening($login);
		$mail=$this->screening($mail);
		if (!filter_var($mail, FILTER_VALIDATE_EMAIL)){
			self::$error='Неверный email.';
			return false;
		}
		//~ select data from this login
		$result = self::$sqli->query("SELECT * FROM `users` WHERE `name`='".$login."'");
    $find_user = $result->fetch_assoc();
		if ($find_user) {
			if ($find_user['mail']!=$mail) {
				//~ Email does not meet this login.
				self::$error='Email не соответствует этому имени';
				return false;
			} else {
				//~ email and login is correct
				$new_passwd = $this->generateCode(8);
				$new_passwd_sql = md5($find_user['key'].$new_passwd); //~ password hash with the private key and user key
				$message="Вы запросили восстановление пароля sogo.su.\nВаш новый пароль: ".$new_passwd;
				if ($this->send_recovery_mail($find_user['mail'], $message)) {
					self::$sqli->query("UPDATE `users` SET `password`='".$new_passwd_sql."' WHERE `uid` = ".$find_user['uid']."");
					return true;
				} else {
					self::$error='Новый пароль был отправлен.';
					return false;
				}
			}
		} else {
			//~ this login - not found
			self::$error='Пользователя не существует';
			return false;
		}
	}

	/**
	 * This method sends an email with a new password for that user.
	 * @return boolean				true or false
	 */
	function send_recovery_mail($mail,$message) {
		if (mail($mail, "Восстановить пароль от", $message, "From: sup@sogo.su\r\n"."Reply-To: sup@sogo.su\r\n"."X-Mailer: PHP/" . phpversion())) {
			return true;
		} else {
			return false;
		}
	}
  	function successful_reg($mail,$message) {
		if (mail($mail, "Вы успешно зарегистрировались на сайте sogo.su. Ваш логин: ", $message, "From: sup@sogo.su\r\n"."Reply-To: sup@sogo.su\r\n"."X-Mailer: PHP/" . phpversion())) {
			return true;
		} else {
			return false;
		}
	}
 
 	/**
 	 *	This method generate random string
 	 * @param		$length				int - length string
 	 * @return	string				result random string
 	 */
	function generateCode($length) { 
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789"; 
		$code = ""; 
		$clen = strlen($chars) - 1;   
		while (strlen($code) < $length) { 
			$code .= $chars[mt_rand(0,$clen)];   
		} 
		return $code; 
	}
  	/**
	 *	This method returns the current error
	 */
	function error_reporting() {
		$r='';
		if (mb_strlen(self::$error)>0) {
			$r.=self::$error;
		}
		if (count(self::$error_arr)>0) {
			#$r.='<h2>The following errors occurred:</h2>'."\n".'<ul>';
			foreach(self::$error_arr as $key=>$value) {
				$r.=''.$value.'<br />';
			}
			#$r.='</ul>';
		}
		return $r;
	}
  

  public function __destruct(){
     return self::$sqli; 
  }
  
}

?>