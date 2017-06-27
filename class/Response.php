<?php
class Response
{
	const SUCCESS			=	200;	
	const OK				=	200;
	const NICKUSED			=	400;
	const EMAILUSED			=	400;
	const MISSPARAM			=	400;
	const DOESNOTEXIST		=	400;
	const NOTAUTH			=	401;
	const NORIGHT			=	403;
	const NOTFOUND			=	404;
	const UNKNOWN			=	501;
	const UNAVAILABLE		=	503;
	const ORGNAMEUSED		=	410;
	
	var $code;
	var $message;
	var $encoding;
	var $contentType;
	
	function __construct($code = Response::UNKNOWN, $message = null)
	{
		$this->setResponseCode($code);
		$this->setMessage($message);
		$this->encoding = null;
		$this->contentType = "json";
	}
	
	function getMessage()
	{
		return ($this->message);
	}
	
	function getResponseCode()
	{
		return ($this->code);
	}
	
	function setMessage($message, $code = null)
	{
		$this->message = $message;
		if ($code !== null)
			$this->setResponseCode($code);
		return $this;
	}
	
	function setResponseCode($code)
	{
		$this->code = $code;
		return $this;
	}
	
	function setEncode($encode)
	{
		$this->encoding = $encode;
	}
	
	function setContentType($contentType)
	{
		$this->contentType = $contentType;
		return $this;
	}
	
	function setError($e)
	{
		$this->setMessage(["url" => "$_SERVER[REQUEST_URI]", "method" => "$_SERVER[REQUEST_METHOD]", "code" => $e->getCode(), "description" => $e->getMessage()], $e->getCode());
	}
	
	function send()
	{
		http_response_code($this->code);
		header('Content-Type: ' . ($this->contentType == 'json' ? 'application/json' : ($this->contentType == "xml" ? 'application/xml' : 'text/html')));
		
		$final = ($this->contentType == "xml" ? $this->toXML($this->message) : json_encode($this->message));
		echo $final;
	}
	
	function arrayToXML($data, &$xml_data)
	{
		foreach( $data as $key => $value ) {
			if (is_numeric($key))
				$key = $value;
			if (is_array($value) || is_object($value))
			{
				$subnode = $xml_data->addChild($key);
				$this->arrayToXML($value, $subnode);
			}
			else
				$xml_data->addChild("$key", htmlspecialchars("$value"));
		 }
		 return ($xml_data->asXML());
	}

	function toXML($data)
	{
		$xml_data = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><data></data>");
		return ($this->arrayToXML($data, $xml_data));
	}
}
?>