<?php
	http_response_code(404);
	echo
		'{
			"url": "'.$_SERVER['REQUEST_URI'].'",
			"method": "'.$_SERVER['REQUEST_METHOD'].'",
			"code": 404,
			"description": "Not Found"
		}';
?>