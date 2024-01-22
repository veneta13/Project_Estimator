<?php
require_once "bootstrap.php";

$result = null;
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
    {
        $result = (new SessionRequestHandler())->checkLoginStatus();
        break;
    }

    case 'POST':
    {
        $result = (new SessionRequestHandler())->login($_POST['email'], $_POST['password']);
        break;
    }

    case 'DELETE':
    {
        (new SessionRequestHandler())->logout();
        $result = true;
        break;
    }

    default:
    {
        $result = false;
        break;
    }
}

echo json_encode(['result' => $result], JSON_UNESCAPED_UNICODE);
