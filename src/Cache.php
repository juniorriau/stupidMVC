<?php

require_once 'Configuration.php';

/**
 * Just a little wrapper around the standard PHP Memcache object
 *
 **/
class Cache {

	/**
	 * Internal memcache object
	 *
	 * @var object
	 **/
	static private $cache = null;
	
	/**
	 * Returns an instance of the memcache object
	 **/
	public static function getInstance() {

		if (class_exists('Memcache') === false) return false;

		if (self::$cache === null) {

			$host = Configuration::get('cache', 'host');
			$port = Configuration::get('cache', 'port');

			self::$cache = new Memcache;
			$connected = @self::$cache->connect($host, $port);
			if ($connected === false) {
				self::$cache = null;
				return false;
			}
		}
		
		return self::$cache;
	}

	/**
	 * Sets a key/value pair in memacache
	 *
	 * @param string $key The cache key
	 * @param mixed $var The variable to store
	 * @param int $expire Seconds to cache the object, or 0 for seconds until midnight
	 * @return bool False on failure, true on success
	 **/
	public static function set($key, $var, $expire = 0) {
		$cache = Cache::getInstance();
		if ($cache === false) return false;
		if ($expire == 0) $expire = self::secondsTillMidnight();
		return $cache->set($key, $var, MEMCACHE_COMPRESSED, $expire);
	}
	
	/**
	 * Deletes a key from the cache
	 *
	 * @param string $key The cache key
	 * @return bool False on failure, true on success
	 **/
	 public static function delete($key) {
		$cache = Cache::getInstance();
		return $cache->delete($key);
	}
	
	/**
	 * Returns an object from the cache
	 *
	 * @param string $key The cache key
	 * @return bool|mixed False on failure, object on success
	 **/
	 public static function get($key) {
		$cache = Cache::getInstance();
		if ($cache === false) return false;
		else return $cache->get($key);
	}

	/**
	 * Cute little helper function to return the number of seconds until midnight
	 *
	 * @return int Seconds till the bewitching hour!
	 **/
	public static function secondsTillMidnight() {
		return strtotime("+1 day midnight") - time();
	}

}

?>
