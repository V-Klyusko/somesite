<?php
class IndexController{

	public $db_host = '127.0.0.4'; 
	public $db_login = 'root';
	public $db_password = '';
	public $db_name = 'blog';
	public $db_encoding = 'utf8';
	
	public function indexAction(){
	$per_page = 3;
	$page_current = isset($_GET['page']) && (int)$_GET['page']>=1 ? (int)$_GET['page'] : 1;
	$db = new Database();
	$db->connect($this->db_host,$this->db_login,$this->db_password,$this->db_name);
	$db->query("SET NAMES " . $this->db_encoding);
	$posts = $db->get_rows("SELECT * from post LIMIT ".($page_current-1)*$per_page.",$per_page");
	$posts_so = sizeof($posts);
	$posts_all_count = $db->get_rows("SELECT count(post_id) AS count FROM post");
	$posts_all_count = (int)$posts_all_count[0]["count"];
	$pages_count = ceil($posts_all_count/$per_page);
	for ($i=0; $i < $posts_so; $i++) {
		$posts[$i]['text'] = array(
			'value' => $posts[$i]['text'],
			'escape' => false,
		);
	}
	$render_data = array(
		'posts' => $posts,
		'posts_so' => $posts_so,
		'pages_count' => $pages_count,
		'page_current' => $page_current,
	);

	$view = new View();
	$content = $view->render('index', $render_data);
	$view->setData('content', $content, false);
	$page_html = $view->render('page');
	header('Content-Type: text/html; charset=utf-8');
	echo $page_html;
	}
}