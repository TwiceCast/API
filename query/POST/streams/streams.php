<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

	$response = new Response(Response::OK);
	try {
		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$postdata = getPostData();
		$authentication = new Authentication();
		$authentication->verify();
		if (!isset($postdata["title"]) || !isset($postdata["lang"]) || !isset($postdata["private"]))
			throw new ParametersException("Parameters missing to proceed", Response::MISSPARAM);
		if ($postdata["private"] === true)
			throw new RightsException("You don't have the rights to create a private stream", Response::NORIGHT);
		$stream = new Stream();
		$stream->setOwner($authentication->getUserFromToken());
		if ($stream->getFromTitle($postdata["title"]))
			throw new ParametersException("You already have a stream with this name", Response::MISSPARAM);
		$stream->setTitle($postdata["title"]);
		if ($postdata["short_description"])
			$stream->setShortDescription($postdata["short_description"]);
		if (!$stream->create())
			throw new UnknownException("Stream cannot be created", Response::UNKNOWN);
		if (isset($postdata["tags"]))
		{
			if (is_array($postdata["tags"]))
			{
				foreach ($postdata["tags"] as &$entry)
				{
					$tag = new Tag();
					if ($tag->getFromId($entry))
					{
						try
						{
							var_dump("adding tag : ".$entry);
							$stream->addTagToDB($entry);
						}
						catch (ParametersException $e)
						{
							
						}
					}
				}
			}
		}
		$response->setMessage($stream);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response->send();
	}
?>