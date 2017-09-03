<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
	
	class Tag
	{
		var $id;
		var $name;
		var $broadcasting;
		var $short_description;
		var $full_description;
		private $linkedtag;
		private $db;

		function __construct($db = true, $id = null, $name = null, $broadcasting = 0, $short_description = null, $full_description = null, $linkedtag = array())
		{
			$this->setId($id);
			$this->setName($name);
			$this->setBroadcasting($broadcasting);
			$this->setShortDescription($short_description);
			$this->setFullDescription($full_description);
			$this->setLinkedTag($linkedtag);
			if ($db)
				$this->db = new DB();
			else
				$this->db = null;
		}

		public function __toString()
		{
			return "tag";
		}

		function setId($id)
		{
			$this->id = $id;
			return $this;
		}

		function setName($name)
		{
			$this->name = $name;
			return $this;
		}

		function setBroadcasting($broadcasting)
		{
			$this->broadcasting = $broadcasting;
			return $this;
		}

		function setShortDescription($short_description)
		{
			$this->short_description = $short_description;
			return $this;
		}

		function setFullDescription($full_description)
		{
			$this->full_description = $full_description;
			return $this;
		}

		function setLinkedTag($linkedtag)
		{
			$this->linkedtag = $linkedtag;
			return $this;
		}

		function addLinkedTag($linkedtag)
		{
			$this->linkedtag[] = $linkedtag;
			return $this;
		}

		function getLinkedTags()
		{
			return $this->linkedtag;
		}

		function getLink($db)
		{
			if ($this->db)
				return $this->db;
			else if ($db)
				return $db;
			else
				return false;
		}

		/*
		** Use linkedtag as a single way link id_tag_a -> id_tag_b
		*/
		function getLinkedFromId($id, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT tag.name AS tagName
					FROM tag_linked
					LEFT JOIN tag ON tag_linked.id_tag_b = tag.id
					WHERE tag_linked.id_tag_a = :id');
				$link->bindParam(':id', $id, PDO::PARAM_INT);
				$data = $link->fetchAll(true);
				if ($data !== false)
				{
					foreach ($data as &$entry)
					{
						$this->addLinkedTag(DB::fromDB($entry['tagName']));
					}
					return true;
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromId($id, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT tag.id AS tagId,
					tag.name AS tagName,
					tag.short_description AS tagShortDescription,
					tag.full_description AS tagFullDescription
					FROM tag
					WHERE tag.id = :id');
				$link->bindParam(':id', $id, PDO::PARAM_INT);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setId($data['tagId']);
					$this->setName(DB::fromDB($data['tagName']));
					$this->setShortDescription(DB::fromDB($data['tagShortDescription']));
					$this->setFullDescription(DB::fromDB($data['tagFullDescription']));
					return $this->getLinkedFromId($id, $link);
				}
				else
					return false;
			}
			else
				return false;
		}

		function getFromName($name, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					SELECT tag.id AS tagId,
					tag.name AS tagName,
					tag.short_description AS tagShortDescription,
					tag.full_description AS tagFullDescription
					FROM tag
					WHERE tag.name = :name');
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$data = $link->fetch(true);
				if ($data)
				{
					$this->setId($data['tagId']);
					$this->setName(DB::fromDB($data['tagName']));
					$this->setShortDescription(DB::fromDB($data['tagShortDescription']));
					$this->setFullDescription(DB::fromDB($data['tagFullDescription']));
					return $this->getLinkedFromId($this->id, $link);
				}
				else
					return false;
			}
			else
				return false;
		}

		function getAllTags($limit = null, $offset = null, $db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$query = '
					SELECT tag.id AS tagId,
					tag.name AS tagName,
					tag.short_description AS tagShortDescription,
					tag.full_description AS tagFullDescription
					FROM tag
					ORDER BY tag.id';
				if ($limit)
				{
					$limit = (int) $limit;
					if ($offset)
					{
						$offset = (int) $offset;
						$quert .=  " LIMIT ".$limit." OFFSET ".$offset;
					}
					else
						$query .= " LIMIT ".$limit;
				}
				$link->prepare($query);
				$data = $link->fetchAll(true);
				if ($data === false)
					return false;
				$tags = array();
				foreach ($data as &$entry)
				{
					$tag = new Tag(false);
					$tag->setId($entry['tagId']);
					$tag->setName(DB::fromDB($entry['tagName']));
					$tag->setShortDescription(DB::fromDB($entry['tagShortDescription']));
					$tag->setFullDescription(DB::fromDB($entry['tagFullDescription']));
					$tag->getLinkedFromId($tag->id, $link);
					$tags[] = $tag;
				}
				return $tags;
			}
			else
				return false;
		}

		function changeName($newName, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				UPDATE tag
				SET tag.name = :name
				WHERE tag.id = :id');
			$tmp = DB::toDB($newName);
			$link->bindParam(':name', $tmp, PDO::PARAM_STR);
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->name = $newName;
		}

		function changeShortDescription($newShortDescription, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				UPDATE tag
				SET tag.short_description = :short_description
				WHERE tag.id = :id');
			$tmp = DB::toDB($newShortDescription);
			$link->bindParam(':short_description', $tmp, PDO::PARAM_STR);
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->short_description = $newShortDescription;
		}

		function changeFullDescription($newFullDescription, $db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				UPDATE tag
				SET tag.full_description = :full_description
				WHERE tag.id = :id');
			$tmp = DB::toDB($newFullDescription);
			$link->bindParam(':full_description', $tmp, PDO::PARAM_STR);
			$llnk->bindParam(':id', $this->id, PDO::PARAM_INT);
			$link->execute(true);
			$this->full_description = $newFullDescription;
		}

		function update($db = null)
		{
			$link = $this->getLink($db);
			if ($link)
			{
				$link->prepare('
					UPDATE tag
					SET tag.name = :name,
					tag.short_description = :short_description,
					tag.full_description = :full_description
					WHERE tag.id = :id');
				$name = DB::toDB($this->name);
				$short_description = DB::toDB($this->short_description);
				$full_description = DB::toDB($this->full_description);
				$link->bindParam(':name', $name, PDO::PARAM_STR);
				$link->bindParam(':short_description', $short_description, PDO::PARAM_STR);
				$link->bindParam(':full_description', $full_description, PDO::PARAM_STR);
				$link->bindParam('id', $this->id, PDO::PARAM_INT);
				return $link->execute(true);
			}
			else
				return false;
		}

		function create($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$this->checkForCreation($this->name);
			$link->prepare('
				INSERT INTO tag(name, short_description, full_description)
				VALUE (:name, :short_description, :full_description)');
			$tmpName = DB::toDB($this->name);
			$tmpShortDescription = DB::toDB($this->short_description);
			$tmpFullDescription = DB::toDB($this->full_description);
			$link->bindParam(':name', $tmpName, PDO::PARAM_STR);
			$link->bindParam(':short_description', $tmpShortDescription, PDO::PARAM_STR);
			$link->bindParam(':full_description', $tmpFullDescription, PDO::PARAM_STR);
			if (!$link->execute(true))
				return false;
			$this->getFromId($link->link->lastInsertId());
			$this->createLinked($link);
			return true;
		}
		
		function createLinked($db = null)
		{
			if (is_array($this->linkedtag))
			{
				$link = $this->getLink($db);
				if (!$link)
					throw new UnknownException("Something wrong happened", Response::UNKNOWN);
				$query = '
					SELECT tag.id AS tagId,
					tag.name AS tagName,
					tag.short_description AS tagShortDescription,
					tag.full_description AS tagFullDescription
					FROM tag
					WHERE tag.name IN ("'.implode('","', $this->linkedtag).'")';
				$link->prepare($query);
				$data = $link->fetchAll(true);
				if ($data)
				{
					foreach ($data as &$row)
					{
						$link->prepare('
							INSERT INTO tag_linked(id_tag_a, id_tag_b)
							VALUE (:a, :b)');
						$link->bindParam(':a', $this->id, PDO::PARAM_INT);
						$link->bindParam(':b', $row['tagId'], PDO::PARAM_INT);
						if (!$link->execute(true))
							return false;
					}
				}
			}
		}
		
		function checkForCreation($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT tag.id AS tagId
				FROM tag
				WHERE tag.name = :name');
			$tmp = DB::toDB($this->name);
			$link->bindParam(':name', $tmp, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Tag name already in use", Response::NICKUSED);
			return true;
		}

		function checkName($nameToCheck, $db = null)
		{
			if ($nameToCheck === $this->name)
				return true;
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				SELECT tag.id AS tagId
				FROM tag
				WHERE tag.name = :name');
			$tmp = DB::toDB($nameToCheck);
			$link->bindParam(':name', $tmp, PDO::PARAM_STR);
			$data = $link->fetchAll(true);
			if ($data)
				throw new ParametersException("Tag name already in use", Response::NICKUSED);
			return true;
		}

		function delete($db = null)
		{
			$link = $this->getLink($db);
			if (!$link)
				throw new UnknownException("Something wrong happened", Response::UNKNOWN);
			$link->prepare('
				DELETE
				FROM tag
				WHRE tag.id = :id');
			$link->bindParam(':id', $this->id, PDO::PARAM_INT);
			return $link->execute(true);
		}
	}
?>