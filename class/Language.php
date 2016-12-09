<?php
	class Language
	{
		var $ID;
		var $name;
		
		function __construct($ID = null, $name = null)
		{
			$this->setID($ID);
			$this->setName($name);
		}

		function setID($ID)
		{
			$this->ID = $ID;
			return $this;
		}

		function setName($name)
		{
			$this->name = $name;
			return $this;
		}
	}