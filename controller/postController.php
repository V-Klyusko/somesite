<?php 
class PostController{
	public $db_host = '127.0.0.6'; 
	public $db_login = 'root';
	public $db_password = '';
	public $db_name = 'somesite';
	public $db_encoding = 'utf8';
	public $per_page = 5;

	public function indexAction(){
		$db = new Database();
		$db->connect($this->db_host, $this->db_login, $this->db_password, $this->db_name);
		$db->query("SET NAMES ".$this->db_encoding);
		$sql = "SELECT * FROM post WHERE url = '" . $db->esc($_GET['url']) . "' LIMIT 1";
		$post = $db->get_rows($sql);
		if(empty($post)){
			include 'controller/errorController.php';
			$error = new ErrorController();
			return $error->notFoundAction();
		}
		$comments_page_current = isset($_GET['comments']) && (int)$_GET['comments']>=1 ? (int)$_GET['comments'] : 1;
		$sql = "SELECT * FROM comment
			INNER JOIN user ON user.user_id=comment.user_id
			WHERE comment.post_id=".$post[0]['post_id']."
			ORDER BY date_posted DESC
			LIMIT ".($comments_page_current-1)*$this->per_page.",".$this->per_page;
		$comments = $db->get_rows($sql);
		$comments_so = sizeof($comments);
		$sql="SELECT count(comment_id) AS count FROM comment WHERE post_id=".$post[0]['post_id'];
		$comments_all_count = $db->get_rows($sql);
		$comments_all_count = (int)$comments_all_count[0]["count"];
		$comments_pages_so = ceil($comments_all_count/$this->per_page);
		$post[0]['text'] = array(
			'value' => $post[0]['text'],
			'escape' => false,
		);
		$render_data = array(
			'comments' => $comments,
			'comments_so' => $comments_so,
			'comments_all_count' => $comments_all_count,
			'comments_page_current' => $comments_page_current,
			'comments_pages_so' => $comments_pages_so,
			'post' => $post[0],
			'comments_action' => 'add',
		);

		$view = new View();
		$content = $view->render('post', $render_data);
		$view->setData('content', $content, false);
		$page_html = $view->render('page');
		header('Content-Type: text/html; charset=utf-8');
		echo $page_html;
	}
}
?>