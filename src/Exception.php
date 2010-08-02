<?php

/**
 * Just an extension of the base PHP Exception class
 **/
class stupidException extends Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		$errors = Configuration::get('errors', 'email');
		$address = Configuration::get('errors', 'address');
		
		if ($errors == 1 && $address !== false) {
			$this->_mailException($address);
		}

		parent::__construct($message, $code, $previous);
	}

	/**
	 * Returns a tring representation of the exception
	 *
	 * @return string representation of the exception
	 **/
	public function toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

	/**
	 * Sends exception information in email
	 *
	 * @return bool Whether or not the email was sent
	 **/
	private function _mailException($address) {
		$to = $address;
		$from = "stupidMVC@wellsoliver.com";
		$subject = sprintf("stupidMVC exception: %s", $this->getMessage());
		$body = "Howdy.";

		if (mail($to, $subject, $body, "From: $from\n") === false) {
			error_log("could not email exception");
			return false;
		}
		
		return true;
	}
}

?>
