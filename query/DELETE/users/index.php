<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');

	$response = new Response(Response::OK);
	try {
		$auth = new Authentication();
		$out = null;

		if (isset($_GET['accept']))
			$response->setContentType($_GET['accept']);
		$auth->verify();
		$user = new User();

		if (($id = (isset($_GET['id']) ? 'id' : (isset($_GET['nickname']) ? 'nickname' : false))) === false)
			throw new ParametersException("Missing parameters to proceed", Response::MISSPARAM);
		if (($id == "id" && !$auth->isUserID($_GET[$id])) || ($id == "nickname" && !$auth->isUserName($_GET[$id])))
			throw new RightsException('You can not delete someone else\'s account', Response::NORIGHT);
		$user = ($id == "id" ? $user->getFromID($_GET[$id]) : $user->getFromNickname($_GET[$id]));
		if (!$user)
			throw new ParametersException("This user does not exist", Response::DOESNOTEXIST);
		if (!$user->delete())
			throw new UnknownException("Something wrong happened", Response::UNKNOWN);
		$response->setMessage(["message" : "User deleted successfully"], Response::SUCCESS);
	} catch (CustomException $e) {
		$response->setError($e);
	} finally {
		$response.send();
	}
?>
