<?php
require_once "bootstrap.php";

$result = null;
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
    {
        $result = (new SessionRequestHandler())->getCurrentProject();
        break;
    }

    case 'POST':
    {
        $result = (new SessionRequestHandler())->saveProject($_POST['project_name'], $_POST['preset']);
        break;
    }

    default:
    {
        $result = "Unknown";
        break;
    }
}

echo json_encode(['result' => $result], JSON_UNESCAPED_UNICODE);
