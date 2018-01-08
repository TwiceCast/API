<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/PHPMailer/PHPMailerAutoload.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/config.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/User.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/functions.php');

    $response = new Response(Response::OK);
    try {
        $postdata = getPostData();

        if (isset($_GET['accept']))
           $response->setContentType($_GET['accept']);
        if (!isset($postdata['email']))
            throw new ParametersException("Missing parameters", Response::MISSPARAM);
        $user = new User();
        if (!$user->getFromEmail($postdata['email']))
            throw new NotFoundException("Unknown email");
        $auth = new Authentication();
        $resetToken = $auth->generateResetToken($user);
        if ($resetToken === false)
            throw new UnknownException("Something wrong append", Response::UNKNOWN);
        $mailer = new PHPMailer();
        $mailer->IsSMTP();
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = "ssl";
        $mailer->SMTPOptions = array(
            'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
        $mailer->Host = $_SESSION["config"]["mail"]["host"];
        $mailer->Port = $_SESSION["config"]["mail"]["port"];
        $mailer->IsHTML(true);
        $mailer->Username = $_SESSION["config"]["mail"]["username"];
        $mailer->Password = $_SESSION["config"]["mail"]["password"];
        $mailer->SetFrom("noreply.twicecast@gmail.com", "TwiceCast");
        $mailer->Subject = "Password reset request";
        $mailer->Body = "Dear $user->name,<br/><br/>You asked to reset your password.<br/>Here is the token that will allow you to change your password:<br/> $resetToken<br/><br/>You can also follow <a href=\"https://twicecast.ovh/reset/$resetToken\">this link</a>.<br/><br/>If you have not requested a password change please disregard this message.<br/><br/>TwiceCast";
        $mailer->AddAddress($user->email);

        if ($mailer->send())
            $response->setMessage("The reset token has been sent by mail");
        else
        {
            throw new CustomException("$mailer->ErrorInfo");
        }
    } catch (CustomException $e) {
        $response->setError($e);
    } finally {
        $response->send();
    }