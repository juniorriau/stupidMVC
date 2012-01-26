<?php

/**
 * Base class for ye standard controller
 **/
abstract class Controller {
	
	/**
	 * Assocative array to store internal key/value pairs eventually passed to the view
	 *
	 * @var array
	 **/
	protected $data = array();

	/**
	 * Name of the controller, e.g. 'index'
	 *
	 * @var string
	 **/
	protected $name = "";

	/**
	 * The associated model object
	 *
	 * @var object
	 **/
	protected $model;

    protected $usewrapper;
	
	private function __construct($name) {
		$this->name = $name;
		$this->usewrapper = true;
	}
	
	/**
	 * Factory method to return a new controller or false on failure
	 *
	 * @return bool|object False on failure, controller object on success
	 **/
	public static function factory($name) {
		$full = sprintf("%s_controller", $name);
		if (class_exists($full) === false) return false;
		return new $full($name);
	}
	
	/**
	 * Returns the associative array of internal key/value pairs
	 *
	 * @return array Protected @data array
	 **/
	public function getData() {
		return $this->data;
	}

	/**
	 * Getter for internal key/value storage
	 *
	 * @return bool|string False on failure, value for key on success
	 **/
	public function get($key) {
		if (isset($this->data[$key]) === false) return false;
		else return $this->data[$key];
	}

	/**
	 * Sets key/value pair on internal storage
	 *
	 * @return Always returns true
	 **/
	public function set($key, $value) {
		$this->data[$key] = $value;
		return true;
	}
	
	/**
	 * Sets associated model
	 *
	 * @return bool If $model is false, false. Otherwise.. true!
	 **/
	public function setModel($model) {
		if ($model === false) return false;
		$this->model = $model;
		return true;
	}

    public function showWrapper() {
        return $this->usewrapper;
    }


    public function noWrapper() {
        $this->usewrapper = false;
    }
	
}

?>
