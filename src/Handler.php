<?php

require_once 'stupidMVC/Cache.php';
require_once 'stupidMVC/Helper.php';
require_once 'stupidMVC/Exception.php';

function __autoload($class) {
	$controller_file = sprintf("%s/controllers/%s.php", STUPID_APPLICATION_PATH, $class);
	$model_file = sprintf("%s/models/%s.php", STUPID_APPLICATION_PATH, $class);

	if (file_exists($controller_file)) require_once $controller_file;
	elseif (file_exists($model_file)) require_once $model_file;
	else return false;
}

/**
 * This is the real deal class. It handles the HTTP request, instantiates the controller and model, processes the request, spits out the response, does your laundry,
 * etc. This is where the magic happens, baby.
 *
 **/
class Handler {
	
	/**
	 * The fully qualified directory where the application is stored
	 *
	 * @var string
	 **/
	private $_application_path;
	
	public function __construct() {
		$this->_application_path = realpath($_SERVER['DOCUMENT_ROOT'] . '/../app');
		define('STUPID_APPLICATION_PATH', $this->_application_path);
		define('STUPID_WEB_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));
		define('STUPID_VIEW_PATH', $this->_application_path . '/views');
		
		$ini = ini_get('include_path');
		$ini .= PATH_SEPARATOR . STUPID_APPLICATION_PATH;
		ini_set('include_path', $ini);
		
		$this->process();
	}
	

	/**
	 * Make it so! Processes the request and spits out the responses.
	 *
	 * @return string The - generally speaking - HTML output that the user sees
	 **/
	private function process() {
		require_once 'Controller.php';
		require_once 'View.php';
		require_once 'Model.php';

		$c = Controller::factory("stupid_kernel");
		if ($c !== false) {
			$c->setModel(Model::factory("stupid_kernel"));
			if (method_exists($c, "process")) $c->process();
		}

		if (isset($_GET['url']) === false) {
			throw new stupidException("Server not properly configured: no URL parameter in the query string.");
			exit();
		}
		
		$url = $_GET['url'];

		if (empty($url)) {
			$name = 'index';
			$action = 'index';
			$params = array();
		} else {
			$url_array = explode("/", $url);
			$name = array_shift($url_array);
			if (count($url_array) < 1) $action = 'index';
			else $action = array_shift($url_array);
			if (empty($action)) $action = 'index';
			if (empty($url_array) === false) $params = $url_array;
			else $params = array();
		}

		$controller = Controller::factory($name);
		
		if ($controller === false) {
			throw new stupidException(sprintf("No such controller: %s", $name));
		}
		
		if (method_exists($controller, $action) === false) {
			throw new stupidException(sprintf("No such action %s for controller %s", $action, $name));
		}
		
		$model = Model::factory($name);
		if ($model !== false) $controller->setModel($model);
		
		call_user_func_array(array($controller, $action), $params);
		$data = $controller->getData();
		
		$view = View::factory($name);
		$view->setData($data);
		$view->render($action);
	}
}

?>
