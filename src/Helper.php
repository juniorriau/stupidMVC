<?php

/**
 * Just a bunch of static helper methods that... help
 **/
abstract class Helper {

	/**
	 * Uh... redirects a use.
	 *
	 * @return void
	 **/
	public static function redirect($url) {
		header("Location: $url");
		exit();
	}
	
	/**
	 * var_dump()s N number of variables to the screen with informative little h2 tags with PHP script/line number in case you get lost
	 * like I do
	 *
	 * @return void
	 **/
	public static function debug($args) {
		$backtrace = debug_backtrace();
		$caller = $backtrace[0];
		$line = sprintf("%s:%s", $caller['file'], $caller['line']);
		echo "<h2>$line</h2>\n\n";
		foreach (func_get_args() as $arg) {
			var_dump($arg);
		}

		die;
	}

}