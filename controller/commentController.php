<?php
class CommentController {
	public $db_host = '127.0.0.4'; 
	public $db_login = 'root';
	public $db_password = '';
	public $db_name = 'blog';
	public $db_encoding = 'utf8';

	public function commentAddAction(){
/*		if (!isset($_POST['text'])) {
			$error = [];
			if(  !empty($_SESSION['error'])  ){
				$error = $_SESSION['error'];
				unset($_SESSION['error']);
			}
			if(isset($_SESSION['comment'])){
				$comment =  $_SESSION['comment'];
			}
			else $comment = array();
			
			$post[0]['text'] = array(
			'value' => $post[0]['text'],
			'escape' => false,
		);

			$render_data = array(
				'err' => $error,
				'comment' => $comment,
				'action' => 'add',
				'post' => $post[0],
			);
			$view = new View();
			$content = $view->render('post', $render_data);
			$view->setData('content', $content, false);
			$page_html = $view->render('page');
			header('Content-Type: text/html; charset=utf-8');
			echo $page_html;
			return;
		}*/

		$text = $_POST['text'];
		
		$err = array();
		if(empty(trim($text))){
			$err['empty_field'] = true;
		}
		if (empty($err)) {
			$sql = "INSERT INTO `comment`(`text`)
			VALUES('$text')";
			$db = new Database();
			$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
			$db->query("SET NAMES " . $this->db_encoding);
			$db->query($sql);
			header('Location: /');
		}
		if(!empty($err)){
			$_SESSION['error'] = $err;
			$_SESSION['comment'] = $_POST;
			header('Location: /comment/add');
			exit();
		}
	}
	
	public function commentUpdateAction(){
		if (!isset($_POST['text'])) {
			$error = [];
			if(  !empty($_SESSION['error'])  ){
				$error = $_SESSION['error'];
				unset($_SESSION['error']);
			}
			$db = new Database();
			$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
			$db->query("SET NAMES ".$this->db_encoding);
			$url = $_GET['uri'];
			$comment = $db->get_rows("SELECT * FROM post WHERE url = '". $db->esc($url) ."'");
			$post[0]['text'] = array(
			'value' => $post[0]['text'],
			'escape' => false,
		);

			$render_data = array(
				'err' => $error,	
				'comment' => $comment[0],
				'action' => $comment . '/update',
				'post' => $post[0],

			);			
			$view = new View();
			$content = $view->render('post', $render_data);
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
			$old_url = $db->esc($_GET['uri']);
			$sql = "UPDATE `comment` 
			SET url='$url_esc', title='$title_esc', text='$text_esc'
			WHERE post.url = '$old_url'";
			$db->query("SET NAMES " . $this->db_encoding);
			$db->query($sql);	
			$target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
			header("Location: /post/". $url);
			exit();
		}
		$_SESSION['error'] = $err;
		header('Location: /post/'. $old_url .'/update');
		exit();	
	}
	public function commentDeleteAction(){
		if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_POST['id'])){
			include 'controller/errorController.php';
			$error = new ErrorController();
			return $error->notFoundAction();
		}	
		$db = new Database();
		$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
		$sql = "DELETE FROM comment
		WHERE comment.comment_id = '" . (int)$_POST['id'] ."'";

		$db = new Database();
		$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
		$db->query("SET NAMES " . $this->db_encoding);
		$db->query($sql);
		$target = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
		header("Location: " . $target);
		exit();
	}

}	
