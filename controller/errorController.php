<?php
class ErrorController {
	public function notFoundAction() {
		$view = new View();
		$content = $view->render('not_found');
		$view->setData('content', $content, false);
		$page_html = $view->render('page');
		header('Content-Type: text/html; charset=utf-8');
		header("HTTP/1.0 404 Not Found");
		return $page_html;
	}
}