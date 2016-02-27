<?php

require(BASE_PATH . '/controllers/BaseController.php');

class Router {

	public static function route($args) {
		$action = array_shift($args);
		if (!$action) {
			$action = 'view';
		}

		$controllerClass = null;

		if ($action == 'view') {
			require(BASE_PATH . '/controllers/ViewGalleryController.php');
			$controllerClass = 'ViewGalleryController';
		}

		if ($action=="thumb") {
			require(BASE_PATH . '/controllers/ThumbController.php');
			$controllerClass = 'ThumbController';
		}

		if ($action=="get") {
			require(BASE_PATH . '/controllers/GetController.php');
			$controllerClass = 'GetController';
		}

		if ($action=="archive") {
			require(BASE_PATH . '/controllers/ArchiveController.php');
			$controllerClass = 'ArchiveController';
		}

		if (!$controllerClass) {
			header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
			die("Action $action is not valid\n");
		}

		try {
			$gallery = new Gallery();
		} catch (Exception $e) {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			die("Gallery not found");
		}

		$controller = new $controllerClass($gallery, $args);
		$controller->handle();
	}

}
