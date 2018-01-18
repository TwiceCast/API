<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/DB.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

    class Block
    {
        var $userId;
        var $blocks;
        private $db;

        function __construct($db = true, $userId = null, $blocks = null)
        {
            $this->setUserId($userId);
            $this->setBlocks($blocks);
            if ($db)
                $this->db = new DB();
            else
                $this->db = null;
        }

        public function __toString()
        {
            return "block";
        }

        function setUserId($userId)
        {
            $this->userId = $userId;
            return $this;
        }

        function setBlocks($blocks)
        {
            if ($blocks == null)
                $this->blocks = array();
            else if (is_array($blocks))
                $this->blocks = $blocks;
            else if (is_object($blocks))
                $this->addBlock($blocks);
            return $this;
        }

        function addBlock($block)
        {
            if (is_object($block))
                $this->blocks[] = $block;
            else if ((int)$block > 0)
            {
                $b = new User(false);
                if ($b->getFromId($block, $this->db))
                    $this->blocks[] = $b;
            }
            return $this;
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

        function getFromId($userId, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    SELECT DISTINCT blocks.id_block AS blockId
                    FROM blocks
                    WHERE blocks.id_client = :id');
                $link->bindParam(':id', $userId, PDO::PARAM_INT);
                $data = $link->fetchAll(true);
                if ($data === false)
                    return false;
                foreach ($data as $entry)
                {
                    $this->addBlock($entry['blockId']);
                }
                return $this->blocks;
            }
            else
                return false;
        }

        function addBlockById($userId, $blockId, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    INSERT INTO blocks(id_client, id_block)
                    VALUES (:userId,:blockId)');
                $link->bindParam(':userId', $userId, PDO::PARAM_INT);
                $link->bindParam(':blockId', $blockId, PDO::PARAM_INT);
                if (!$link->execute(true))
                    return false;
                $this->getFromId($userId);
                return true;
            }
        }

        function removeBlockById($userId, $blockId, $db = null)
        {
            $link = $this->getLink($db);
            if ($link)
            {
                $link->prepare('
                    DELETE FROM blocks
                    WHERE blocks.id_client = :userId
                    AND blocks.id_block = :blockId');
                $link->bindParam(':userId', $userId, PDO::PARAM_INT);
                $link->bindParam(':blockId', $blockId, PDO::PARAM_INT);
                if (!$link->execute(true))
                    return false;
                $this->getFromId($userId);
                return true;
            }
        }
    }
?>