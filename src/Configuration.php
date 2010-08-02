<?php

/**
 * Configuration object - Just a wee little object that's used to maintain information parsed from an INI file. The object looks in
 * app/config/local.ini to find the information.
 *
 **/
class Configuration {

	/**
	 * Internal configuration object
	 *
	 * @var object
	 **/
	static private $manager = null;

	private function __construct() {
	
		$ini_file = STUPID_APPLICATION_PATH . '/config/local.ini';
		if (file_exists($ini_file) == false) return;
		
		$config = @parse_ini_file($ini_file, true);
		if ($config === false) die(sprintf("configuration file %s seems to be an invalid INI file", $ini_file));
		
		$this->configuration = $config;
	}

	/**
	 * Returns an instance of the configuration object
	 **/
	private static function getInstance() {
		if (self::$manager === null) {
			self::$manager = new configuration();
		}

		return self::$manager;
	}
	
	/**
	 * Returns a value for a given section and key combination from the configuration object
	 * @param string $section The configuration section
	 * @param string $key The configuration key
	 *
	 * @return bool|string False on failure, string on success
	 **/
	public static function get($section, $key) {
		$man = configuration::getInstance();

		if (isset($man->configuration[$section]) === false) return false;
		elseif (isset($man->configuration[$section][$key]) === false) return false;
		else return $man->configuration[$section][$key];
	}

}

?>
