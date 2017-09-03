<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Tag.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');

	$response = new Response(Response::OK);
	try {
		$tag = new Tag();
		$authentication = null;

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$authentication = new Authentication();
		$authentication->verify(false);
		if (isset($_GET['id']))
		{
			if (!$tag->getFromId($_GET['id']))
				throw new ParametersException("This tag does not exist", Response::DOESNOTEXIST);
			$tmp = [];
			$tmp['id'] = $tag->id;
			$tmp['name'] = $tag->name;
			$tmp['broadcasting'] = $tag->broadcasting;
			$tmp['short_description'] = $tag->short_description;
			$tmp['full_description'] = $tag->full_description;
			$tmp['linked-tag'] = $tag->getLinkedTags();
			$response->setMessage($tmp);
		}
		else
		{
			if (isset($_GET['limit']))
			{
				if (isset($_GET['start']))
					$tags = $tag->getAllTags($_GET['limit'], $_GET['start']);
				else
					$tags = $tag->getAllTags($_GET['limit']);
			}
			else
				$tags = $tag->getAllTags();
			$rep = new stdClass();
			if ($tags === false)
			{
				$rep->tag_list = null;
				$rep->tag_total = 0;
			}
			else
			{
				$rep->tag_list = array();
				$rep->tag_total = 0;
				foreach ($tags as &$tag)
				{
					$tmp = [];
					$tmp['id'] = $tag->id;
					$tmp['name'] = $tag->name;
					$tmp['broadcasting'] = $tag->broadcasting;
					$tmp['short_description'] = $tag->short_description;
					$tmp['full_description'] = $tag->full_description;
					$tmp['linked-tag'] = $tag->getLinkedTags();
					$rep->tag_list[] = $tmp;
					$rep->tag_total++;
				}
			}
			$response->setMessage($rep);
		}
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>