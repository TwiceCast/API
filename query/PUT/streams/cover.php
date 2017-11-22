<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Stream.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Response.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Exception.php');
    require_once($_SERVER['DOCUMENT_ROOT'].'/class/Authentication.php');

    $response = new Response(Response::OK);
    try
    {
        if (isset($_GET['accept']))
            $response->setContentType($_GET['accept']);
    
        $authentication = new Authentication();
        $authentication->verify();
    
        if (!isset($_GET['id']))
            throw new ParametersException("Mising parameters to proceed");
        $user = $authentication->getUserFromToken();
    
        if (!$user)
            throw new UnknownException("Something wrong happened");
        
        $stream = new Stream();
        if (!$stream->getFromId($_GET['id']))
            throw new NotFoundException("Stream not found", Response::NOTFOUND);

        if ($authentication->userHasRights(array(8,9), $stream->id, "stream") === false)
            throw new RightsException("You don't have enough rights to modify this stream", Response::NORIGHT);

        $filetmp = $_SERVER['DOCUMENT_ROOT']."/cover/tmp/".$stream->id;
        $file = $_SERVER['DOCUMENT_ROOT']."/cover/".$stream->id;
        
        if (!$putdata = fopen("php://input", "rb"))
            throw new UnknownException("Unable to read body", Response::UNKNOWN);

        if (!$fp = fopen($filetmp, "wb+"))
            throw new UnknownException("Unable to create file on the server", Response::UNKNOWN);

        while ($data = fread($putdata, 1024))
        {
            fwrite($fp, $data);
        }

        fclose($fp);
        fclose($putdata);

        $imgType = exif_imagetype($filetmp);
        switch ($imgType)
        {
            case 1:
                $file .= '.gif';
                break;
            case 2:
                $file .= '.jpeg';
                break;
            case 3:
                $file .= '.png';
                break;
            default:
                throw new ParametersException("We only support gif, jpeg and png", Response::UNSUPPORTED);
                break;
        }

        array_map('unlink', glob($_SERVER['DOCUMENT_ROOT']."/cover/".$stream->id.".*"));
        if (!rename($filetmp, $file))
            throw new UnknownException("Something wrong happened");
        $response->setMessage("The cover has been replaced.");
    }
    catch (CustomException $e)
    {
        $response->setError($e);
    }
    finally
    {
        $response->send();
    }