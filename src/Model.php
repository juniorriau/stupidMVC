<?php

require_once 'Configuration.php';

/**
 * Base class for ye  model for all data-related stuff. Contains a bunch of helper-ish DB-wrapper functions
 **/
abstract class Model {

	/**
	 * Name of the controller, e.g. 'index'
	 *
	 * @var string
	 **/
	protected $name;

	/**
	 * The PDO connection object
	 *
	 * @var object
	 **/
	private $handler;
	
	private function __construct($name) {
		$this->name = $name;
	}
	

	/**
	 * Tries to connect to the database
	 *
	 * @return bool Whether we could connect; dies on failure
	 **/
	private function connect() {
		$host = Configuration::get('database', 'host');
		$user = Configuration::get('database', 'user');
		$password = Configuration::get('database', 'password');
		$database = Configuration::get('database', 'database');
		$driver = Configuration::get('database', 'driver');
		if ($driver === false) $driver = 'mysql';
		
		$string = sprintf("%s:host=%s;dbname=%s", $driver, $host, $database);
		try {
			$this->handler = new PDO($string, $user, $password);
		} catch (PDOException $e) {
			error_log("PDO connection error: " . $e->getMessage());
			throw new stupidException($e->getMessage(), $e->getCode(), $e);
			die;
		}
		
		return true;
	}
	

	/**
	 * Takes a query and a field name as key and gives you a string value for the column in the first row, or false
	 *
	 * @return bool|string False when there's no result, otherwise the value for the column in the first row
	 **/
	protected function fetch_column($query, $key) {
		$results = $this->fetch_all($query);
		if (count($results) == 0) return false;

		$row = array_shift($results);
		if (isset($row[$key]) === false) return false;
		else return $row[$key];
	}
	
	/**
	 * Gives you the first row of a database query
	 *
	 * @return bool|string False when there's no result, otherwise an associative array of the result
	 **/
	protected function fetch_one($query) {
		$results = $this->fetch_all($query);
		if (count($results) == 0) return false;
		else return array_shift($results);
	}
	
	/**
	 * Gives you the all rows from the query
	 *
	 * @return bool|string False on failure, or all rows in the result as an associative array of associative arrays
	 **/
	protected function fetch_all($query) {
		if (isset($this->handler) === false) {
			$ok = $this->connect();
			if ($ok === false) return false;
		}
		
		$sth = $this->handler->prepare($query);
		$sth->execute();

		$ret = array();
		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			$ret[] = $row;
		}
		
		return $ret;
	}

	/**
	 * Factory method to return a new model or false on failure
	 *
	 * @return bool|object False on failure, model object on success
	 **/
	public static function factory($name) {
		$full = sprintf("%s_model", $name);
		if (class_exists($full) === false) return false;
		else return new $full($name);
	}
}

?>
