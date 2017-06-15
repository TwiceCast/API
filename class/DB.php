<?php
	class DB
	{
		var $link;
		var $query;
		var $executed;

		function __construct($host = 'localhost', $dbname = 'twicecast', $user = 'api', $password = '')
		{
			$this->query = null;
			$this->executed = false;
			try
			{
				// $this->link = new PDO('pgsql:host='.$host.';dbname='.$dbname, $user, $password);
				$this->link = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $password);
			}
			catch(Exception $e)
			{
				die('Error :'.$e->getMessage());
			}
		}

		function query($prepare, $params, $fetchMode = true)
		{
			if ($this->query != null)
			{
				$this->query->closeCursor();
				$this->executed = false;
			}
			$this->query = $this->link->prepare($prepare);
			foreach ($params as $param)
			{
				$this->query->bindParam($param->key, $param->value, $param->type);
			}
			$ret = $this->query->execute();
			if ($fetchMode)
				$data = $this->query->fetchAll();
			else
				$data = $this->query->fetch();
			$this->query->closeCursor;
			$this->query = null;
			if ($ret === false)
				return false;
			if ($data)
				return $data;
			else
				return false;
		}

		function prepare($string)
		{
			if ($this->query != null)
			{
				$this->query->closeCursor();
				$this->executed = false;
			}
			$this->query = $this->link->prepare($string);
			return true;
		}

		function bindParam($key, $value, $type)
		{
			if ($this->query != null)
			{
				$this->query->bindParam($key, $value, $type);
				return true;
			}
			else
				return false;
		}

		function bindValue($key, $value, $type)
		{
			if ($this->query != null)
			{
				$this->query->bindValue($key, $value, $type);
				return true;
			}
			else
				return false;
		}

		function execute($close = false)
		{
			if ($this->query != null)
			{
				if ($this->executed == false)
				{
					if ($this->query->execute())
					{
						$this->executed = true;
						if($close)
							return $this->closeCursor();
						else
							return true;
					}
					else
						return false;
				}
				else
					return false;
			}
			else
				return false;
		}

		function fetch($close = false)
		{
			if ($this->query != null)
			{
				$this->execute();
				$data = $this->query->fetch();
				if ($close)
					$this->closeCursor();
				return $data;
			}
			else
				return false;
		}

		function fetchAll($close = false)
		{
			if ($this->query != null)
			{
				$this->execute();
				$data = $this->query->fetchAll();
				if ($close)
					$this->closeCursor();
				return $data;
			}
			else
				return false;
		}

		function closeCursor()
		{
			if ($this->query != null)
			{
				$this->query->closeCursor();
				$this->query = null;
				$this->executed = false;
			}
			return true;
		}

		static function fromDB($str)
		{
			return nl2br(DB::entities($str));
		}

		static function fromDBNoBr($str)
		{
			return DB::entities($str);
		}

		static function entities($str)
		{
			return htmlspecialchars(html_entity_decode($str, ENT_QUOTES, "UTF-8"));
		}

		static function toDB($str)
		{
			return htmlentities($str, ENT_QUOTES, "UTF-8", false);
		}
	}
?>