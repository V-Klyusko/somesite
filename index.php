<?php
session_start();
require 'lib/autoload.php';
$url = parse_url($_SERVER['REQUEST_URI']);

$path = isset($url['path']) ? $url['path'] : "/";
$query = isset($url['query']) ? $url['query'] : null;

$url = explode("/", $path);
$result = '';
switch($url[1]) {
	case 'auth':
		include 'controller/authController.php';
		$controller = new AuthController();
		$controller->authAction();
		break;
	case 'registration':
        include 'controller/authController.php';
        $controller = new AuthController();
        $controller->registrAction();
        break;

    case 'request':
    include 'controller/requestController.php';
    $controller = new RequestController();
    $controller->requestAction();
    break;

	case 'comment':
		if(!empty($url[2]) && $url[2]==='add'){
			include 'controller/adminController.php';
			$controller = new CommentController();
			$result = $controller->commentAddAction();
		}
		elseif(!empty($url[2]) && !empty($url[3]) && $url[3]==='edit'){
			$_GET['uri'] = $url[2];
			include 'controller/commentController.php';
			$controller = new CommentController();
			$result = $controller->commentEditAction();
		}
		elseif(!empty($url[2]) && !empty($url[3]) && $url[3]==='delete'){
			$_GET['uri'] = $url[2];
			include 'controller/commentController.php';
			$controller = new CommentController();
			$result = $controller->commentDeleteAction();
		}
		elseif(!empty($url[2])){
			$_GET['url'] = $url[2];
			include 'controller/postController.php';
			$controller = new PostController();
			$result = $controller->indexAction();
	}
		else include 'controller/indexController.php';
		break;

		case 'blog':
		if(empty($url[2])){
			include 'controller/indexController.php';
			$controller = new IndexController();
			$controller->indexAction();
			break;
			}

		
	case '':
		include 'controller/indexController.php';
		$controller = new IndexController();
		$controller->indexAction();
		break;
	case 'logout':
		include 'controller/authController.php';
		$controller = new AuthController();
		$controller->logoutAction();
		break;		
	case 'post':		
		if(empty($url[2])){
			include 'controller/indexController.php';
			$controller = new IndexController();
			$controller->indexAction();
			break;
		}
		if(!empty($url[2]) && $url[2]==='add'){
			include 'controller/adminController.php';
			$controller = new AdminController();
			$result = $controller->postAddAction();
		}
		elseif(!empty($url[2]) && !empty($url[3]) && $url[3]==='update'){
			$_GET['uri'] = $url[2];
			include 'controller/adminController.php';
			$controller = new AdminController();
			$result = $controller->postUpdateAction();
		}
		elseif(!empty($url[2]) && !empty($url[3]) && $url[3]==='delete'){
			$_GET['uri'] = $url[2];
			include 'controller/adminController.php';
			$controller = new AdminController();
			$result = $controller->postDeleteAction();
		}
		elseif(!empty($url[2])){
			$_GET['url'] = $url[2];
			include 'controller/postController.php';
			$controller = new PostController();
			$result = $controller->indexAction();
		}
		else include 'controller/indexController.php';
		break;
	
	default:
		include 'controller/errorController.php';
		$controller = new ErrorController();
		$result = $controller->notFoundAction();
		break;
}
echo $result;