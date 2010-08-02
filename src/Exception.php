<?php

/**
 * Just an extension of the base PHP Exception class
 **/
class stupidException extends Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		$errors = Configuration::get('errors', 'email');
		$address = Configuration::get('errors', 'address');
		$from = Configuration::get('errors', 'from');
		
		if ($errors == 1 && $address !== false) {
			$this->_mailException($address, $from);
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
	private function _mailException($address, $from) {
		$to = $address;
		$subject = sprintf("stupidMVC exception: %s", $this->getMessage());
		$body = "A stupidMVC exception occured:\n\n" . $this->getMessage();

		if (mail($to, $subject, $body, "From: $from\n") === false) {
			error_log("could not email exception");
			return false;
		}
		
		return true;
	}
}

?>
