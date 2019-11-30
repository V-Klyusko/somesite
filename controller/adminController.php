<?php
class AdminController {
	public $db_host = '127.0.0.4'; 
	public $db_login = 'root';
	public $db_password = '';
	public $db_name = 'blog';
	public $db_encoding = 'utf8';

	public function postAddAction(){
		if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
			include 'controller/errorController.php';
			$error = new ErrorController();
			return $error->notFoundAction();
		}		
		if (!isset($_POST['url'], $_POST['title'], $_POST['text'])) {
			$error = [];
			if(  !empty($_SESSION['error'])  ){
				$error = $_SESSION['error'];
				unset($_SESSION['error']);
			}
			if(isset($_SESSION['post'])){
				$post =  $_SESSION['post'];
			}
			else $post = array();

			$render_data = array(
				'err' => $error,
				'post' => $post,
				'action' => 'add',
			);
			$view = new View();
			$content = $view->render('post_edit', $render_data);
			$view->setData('content', $content, false);
			$page_html = $view->render('page');
			header('Content-Type: text/html; charset=utf-8');
			echo $page_html;
			return;
		}

		$url = $_POST['url'];
		$title = $_POST['title'];
		$text = $_POST['text'];
		
		$err = array();
		if(empty(trim($url)) || empty(trim($title)) || empty(trim($text))){
			$err['empty_field'] = true;
		}
		
		
		
		$post_check_query = "SELECT * FROM `post` 
		WHERE `url` = '".  $url  ."' 
		OR `title` = '".  $title  ."'";
		$db = new Database();
		$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
		$db->query("SET NAMES " . $this->db_encoding);
		$data = $db->get_rows($post_check_query);
		
		if(isset($data[0]['url']) && $data[0]['url'] == $url){
				$err['wrong_check_url'] = true;
		}
		if(isset($data[0]['title']) && $data[0]['title'] == $title){
				$err['wrong_check_title'] = true;
		}
		
		
		
		if (empty($err)) {
			$sql = "INSERT INTO `post`(`url`, `title`, `text`)
			VALUES('$url', '$title', '$text')";
			$db = new Database();
			$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
			$db->query("SET NAMES " . $this->db_encoding);
			$db->query($sql);
			header('Location: /');
		}
		if(!empty($err)){
			$_SESSION['error'] = $err;
			$_SESSION['post'] = $_POST;
			header('Location: /post/add');
			exit();
		}
	}
	
	public function postUpdateAction(){
		if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
			include 'controller/errorController.php';
			$error = new ErrorController();
			return $error->notFoundAction();
		}
		if (!isset($_POST['url'], $_POST['title'], $_POST['text'])) {
			$error = [];
			if(  !empty($_SESSION['error'])  ){
				$error = $_SESSION['error'];
				unset($_SESSION['error']);
			}
			$db = new Database();
			$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
			$db->query("SET NAMES ".$this->db_encoding);
			$url = $_GET['uri'];
			$post = $db->get_rows("SELECT * FROM post WHERE url = '". $db->esc($url) ."'");
			// $post[0]['url'] = htmlspecialchars($post[0]['url']);
			// $post[0]['title'] = htmlspecialchars($post[0]['title']);
			// $post[0]['text'] = htmlspecialchars($post[0]['text'], ENT_QUOTES);
			$render_data = array(
				'err' => $error,	
				'post' => $post[0],
				'action' => $url . '/update',
			);			
			$view = new View();
			$content = $view->render('post_edit', $render_data);
			// var_dump($content);die();
			$view->setData('content', $content, false);
			$page_html = $view->render('page');
			header('Content-Type: text/html; charset=utf-8');
			echo $page_html;
			return;
		}
		$url = $_POST['url'];
		$title = $_POST['title'];
		$text = $_POST['text'];
		$err = array();
		if(empty(trim($url)) || empty(trim($title)) || empty(trim($text))){
			$err['empty_field'] = true;
		}
		if (empty($err)) {
			$db = new Database();
			$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
			$url_esc = $db->esc($url);
			$title_esc = $db->esc($title);
			$text_esc = $db->esc($text);
			$old_url_esc = $db->esc($_GET['uri']);
			$sql = "UPDATE `post` 
			SET url='$url_esc', title='$title_esc', text='$text_esc'
			WHERE post.url = '$old_url_esc'";
			$db->query("SET NAMES " . $this->db_encoding);
			$db->query($sql);	
			$target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
			header("Location: /post/". $url);
			exit();
		}
		$_SESSION['error'] = $err;
		header('Location: /post/'. $old_url_esc .'/update');
		exit();	
	}
	
	public function postDeleteAction(){
		if (!isset($_POST['url'], $_POST['title'], $_POST['text'])) {
			$error = [];
			if(  !empty($_SESSION['error'])  ){
				$error = $_SESSION['error'];
				unset($_SESSION['error']);
			}
		} 
		$db = new Database();
		$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);		
		$sql = "DELETE FROM post
		WHERE post.url = '" . $db->esc($_GET['uri']) ."'";

		$db = new Database();
		$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
		$db->query("SET NAMES " . $this->db_encoding);
		$db->query($sql);
		header('Location: /');

		exit();

	}
}