<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Tag.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		$postdata = getPostData();
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);

		$authentication = new Authentication();
		$authentication->verify();
		$user = $authentication->getUserFromToken();

		if (!$user)
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);

		if (!isset($postdata['name']) or !isset($postdata['short_description']) or !isset($postdata['full_description']))
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);

		$tag = new Tag();
		$tag->setName($postdata['name']);
		$tag->setShortDescription($postdata['short_description']);
		$tag->setFullDescription($postdata['full_description']);
		if (isset($postdata['linked-tag']))
		{
			if (is_array($postdata['linked-tag']))
				$tag->setLinkedTag($postdata['linked-tag']);
			else
				$tag->addLinkedTag($postdata['linked-tag']);
		}

		if (!$tag->create())
			throw new UnknownException("Somethign wrong happened", Response::UNKNOWN);

		$tmp = [];
		$tmp['id'] = $tag->id;
		$tmp['name'] = $tag->name;
		$tmp['broadcasting'] = $tag->broadcasting;
		$tmp['short_description'] = $tag->short_description;
		$tmp['full_description'] = $tag->full_description;
		$tmp['linked-tag'] = $tag->getLinkedTags();
		$response->setMessage($tmp);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>