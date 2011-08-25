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
     * Executes a query, usually an INSERT
     *
     * @return integer Number of rows affected
     */
    protected function query($query, $params = array()) {
        if (is_array($params) === false) $params = array($params);

		if (isset($this->handler) === false) {
			$ok = $this->connect();
			if ($ok === false) return false;
		}

     	$sth = $this->handler->prepare($query);
		$sth->execute($params);


		$errrorinfo = $sth->errorInfo();
        if ($errrorinfo[1] != 0) throw new stupidException($errrorinfo[2]);

	    return $sth->rowCount();   
    }

	/**
	 * Takes a query and a field name as key and gives you a string value for the column in the first row, or false
	 *
	 * @return bool|string False when there's no result, otherwise the value for the column in the first row
	 **/
	protected function fetch_column($query, $params, $key) {
		$results = $this->fetch_all($query, $params);
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
	protected function fetch_one($query, $params = array()) {
		$results = $this->fetch_all($query, $params);
		if (count($results) == 0) return false;
		else return array_shift($results);
	}
	
	/**
	 * Gives you the all rows from the query
	 *
	 * @return bool|string False on failure, or all rows in the result as an associative array of associative arrays
	 **/
	protected function fetch_all($query, $params = array()) {
		if (is_array($params) === false) $params = array($params);

		if (isset($this->handler) === false) {
			$ok = $this->connect();
			if ($ok === false) return false;
		}
		
		$sth = $this->handler->prepare($query);
		$sth->execute($params);

		$errrorinfo = $this->handler->errorInfo();
        if ($errrorinfo[1] != 0) throw new stupidException($errrorinfo[2]);

        return $sth->fetchAll(PDO::FETCH_ASSOC);
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
