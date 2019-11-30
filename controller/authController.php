<?php
class AuthController{
	public $db_host = '127.0.0.4'; 
	public $db_login = 'root';
	public $db_password = '';
	public $db_name = 'blog';
	public $db_encoding = 'utf8';
	
		/* Авторизация */
	public function authAction(){
		if (!isset($_POST['login'], $_POST['password'])) {
			$error = [];
			if(  !empty($_SESSION['error'])  ){
				$error = $_SESSION['error'];
				unset($_SESSION['error']);
			}
			$render_data = array(
				'err' => $error,			
			);
			$view = new View();
			$content = $view->render('auth', $render_data);
			$view->setData('content', $content, false);
			$page_html = $view->render('page');
			header('Content-Type: text/html; charset=utf-8');
			echo $page_html;
			return;
		}
		$login = $_POST['login'];
		$password = $_POST['password'];
		$err = array();
		if(empty(trim($login)) || empty(trim($password))){
			$err['empty_field'] = true;
		}
		if (empty($err)) {
			$mhash = bin2hex(mhash(MHASH_WHIRLPOOL, $password));
			$db = new Database();
			$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
			$db->query("SET NAMES " . $this->db_encoding);
			
			$sql = "SELECT * FROM `user` 
			WHERE `login` = '".  $db->esc($_POST['login'])  ."' 
			AND `password` = '".  $mhash. "'";
			$data = $db->get_rows($sql);
			if(empty($data)){
				$err['wrong_data'] = true;
			}
		}
		if(!empty($err)){
			$_SESSION['error'] = $err;
			header('Location: /auth');
			exit();
		}
		$_SESSION['user_id'] = $data[0]['user_id'];
		$_SESSION['nickname'] = $data[0]['nickname'];
		$_SESSION['role'] = $data[0]['role'];
		header('Refresh: 1.75;url=/');
		echo "<h3 align=\"center\" >Здравствуйте, " . $data[0]['nickname'] . "</h3>";
		exit();
	}

	
	/* Регистрация */
	public function registrAction(){
		if (!isset($_POST['login'], $_POST['password'], $_POST['nickname'])) {
			$error = [];
				if(  !empty($_SESSION['error'])  ){
					$error = $_SESSION['error'];
					unset($_SESSION['error']);
				}
			$render_data = array(
				'err' => $error,			
			);
			$view = new View();
			$content = $view->render('registration', $render_data);
			$view->setData('content', $content, false);
			$page_html = $view->render('page');
		//	header('Content-Type: text/html; charset=utf-8');
			echo $page_html;
			return; 
		}
		$login = $_POST['login'];
		$password = $_POST['password'];
		$nickname = $_POST['nickname'];
		$err = array();
		if(empty(trim($login)) || empty(trim($password)) || empty(trim($nickname))){
			$err['empty_field'] = true;
		}	
		
		$user_check_query = "SELECT * FROM `user` 
		WHERE `login` = '".  $login  ."' 
		OR `nickname` = '".  $nickname  ."'";
		$db = new Database();
		$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
		$db->query("SET NAMES " . $this->db_encoding);
		$data = $db->get_rows($user_check_query);
		
		if(isset($data[0]['login']) && $data[0]['login'] == $login){
				$err['wrong_check_login'] = true;
		}
		if(isset($data[0]['nickname']) && $data[0]['nickname'] == $nickname){
				$err['wrong_check_nickname'] = true;
		}
		
		if (empty($err)) {
			$mhash = bin2hex(mhash(MHASH_WHIRLPOOL, $password));
			$db = new Database();
			$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
			$db->query("SET NAMES " . $this->db_encoding);
			$login_esc = $db->esc($login);
			$nickname_esc = $db->esc($nickname);
			$sql = "INSERT INTO `user`(`login`, `nickname`, `password`)
			VALUES('$login_esc', '$nickname_esc', '$mhash')";
			$result = $db->query($sql);
			if (!$result) $err['add_user_fail'] = true;
		}
		if (empty($err)) {
			$_SESSION['nickname'] = $_POST['nickname'];
			header('Location: /');
			exit();
		}
		$_SESSION['error'] = $err;
		header('Location: /registration');
		exit();
	}
	
	
	/* Выход с аккаунта */
	public function logoutAction(){
		unset($_SESSION['role'], $_SESSION['user_id'], $_SESSION['nickname']);
		$target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
		header("Location: " . $target);
		exit();
	}
}
