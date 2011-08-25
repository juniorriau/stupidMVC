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
	    $wrappercontent = $templatecontent = "";

		$template_file = sprintf("%s/%s.tpl.php", $this->template_location, $action);
		$wrapper_file =  sprintf("%s/helpers/wrapper.tpl.php", STUPID_VIEW_PATH);
		
		if (file_exists($template_file)) {
		    ob_start();
			require_once $template_file;
			$templatecontent = ob_get_contents();
			ob_end_clean();
		}
		
		if (file_exists($wrapper_file)) {
		    ob_start();
			require_once $wrapper_file;
			$wrappercontent = ob_get_contents();
			ob_end_clean();
			
			$wrappercontent = str_replace("%%CONTENT%%", $templatecontent, $wrappercontent);
		}
		
		echo (empty($wrappercontent)) ? $templatecontent : $wrappercontent;
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

    public function __get($name) {
        return $this->get($name);
    }

}

?>
