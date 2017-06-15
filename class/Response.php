<?php
class Response
{
	const SUCCESS			=	200;	
	const OK						=	200;
	const NICKUSED			=	400;
	const EMAILUSED		=	400;
	const MISSPARAM		=	400;
	const DOESNOTEXIST	=	400;
	const NOTAUTH			=	401;
	const NORIGHT			=	403;
	const UNKNOWN			=	501;
	const ORGNAMEUSED	=	410;
	
	var $type;
	var $message;
	var $encoding;
	var $contentType;
	
	function __construct($type = Response::UNKNOWN, $message = null)
	{
		$this->setResponseType($type);
		$this->setMessage($message);
		$this->encoding = null;
		$this->contentType = "json";
	}
	
	function getMessage()
	{
		return ($this->message);
	}
	
	function getResponseType()
	{
		return ($this->type);
	}
	
	function setMessage($message, $type = null)
	{
		$this->message = $message;
		if ($type !== null)
			$this->setResponseType($type);
		return $this;
	}
	
	function setResponseType($type)
	{
		$this->type = $type;
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
	
	function send()
	{
		http_response_code($this->type);
		header('Content-Type: ' . ($this->contentType == 'json' ? 'application/json' : ($this->contentType == "xml" ? 'application/xml' : 'text/html')));
		
		$final = ($this->contentType == "json" ? json_encode($this->message) : $this->toXML($this->message));
		//TODO : Encoding (gzip, deflated, etc...);
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