<?php

/**
 * Base class for ye standard view
 **/
class View {

	/**
	 * Name of the view, e.g. 'index'
	 *
	 * @var string
	 **/
	private $name;

	/**
	 * Fully-qualified location of the view file
	 *
	 * @var string
	 **/
	private $template_location;

	/**
	 * All key/value pairs passed from the controller
	 *
	 * @var string
	 **/
	private $data;
	
	private function __construct() {
	}

	/**
	 * Factory method to return a new view or false on failure
	 *
	 * @return bool|object False on failure, view object on success
	 **/
	public static function factory($name) {
		$view = new View();
		$view->name = $name;
		$view->template_location = sprintf("%s/%s", STUPID_VIEW_PATH, $name);
		
		return $view;
	}
	

	/**
	 * Draw out the view by way of requiring the actual PHP file
	 *
	 * @return void
	 **/
	public function render($action) {
		$template_file = sprintf("%s/%s.tpl.php", $this->template_location, $action);
		
		if (file_exists($template_file)) {
			require_once $template_file;
		}
	}
	
	/**
	 * Sets the internal associative array of key/value pairs
	 *
	 * @return void
	 **/
	public function setData($data) {
		$this->data = $data;
	}
	
	
	/**
	 * Getter method to return value for a key
	 *
	 * @return bool|string False when the key doesn't exist, otherwise string value
	 **/
	 public function get($key) {
		if (isset($this->data[$key])) return $this->data[$key];
		else return false;
	}
}

?>
