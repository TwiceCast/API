<?php
	interface IException
	{
		/* Protected methods inherited from Exception class */
		public function getMessage();                 // Exception message
		public function getCode();                    // User-defined Exception code
		public function getFile();                    // Source filename
		public function getLine();                    // Source line
		public function getTrace();                   // An array of the backtrace()
		public function getTraceAsString();           // Formated string of trace

		/* Overrideable methods inherited from Exception class */
		public function __toString();                 // formated string for display
		public function __construct($message, $code = 0, Exception $previous = null);
	}

	class CustomException extends Exception implements IException
	{
		public function __construct($message, $code = 0, Exception $previous = null)
		{
			parent::__construct($message, $code, $previous);
		}

		public function __toString()
		{
			return __CLASS__.": [{$this->code}]: {$this->message}\n";
		}
	}

	class AuthenticationException extends CustomException {}
	class DatabaseException extends CustomException {}
	class UnknownException extends CustomException {}
	class ParametersException extends CustomException {}
	class RightsException extends CustomException {}
?>