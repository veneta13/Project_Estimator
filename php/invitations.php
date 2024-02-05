<?php
require_once "bootstrap.php";

$result = null;
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
    {
        $result = (new SessionRequestHandler())->getUserInvitations();
        break;
    }

    case 'PUT':
    {
        $id = json_encode(file_get_contents("php://input"), true);
        $id = substr($id, 1, -1);
        $id = (int)$id;
        $result = (new SessionRequestHandler())->acceptInvitation($id);
        break;
    }

    case 'DELETE':
    {
        $id = json_encode(file_get_contents("php://input"), true);
        $id = substr($id, 1, -1);
        $id = (int)$id;
        $result = (new SessionRequestHandler())->denyInvitation($id);
        break;
    }

    default:
    {
        $result = "Unknown";
        break;
    }
}

echo json_encode(['result' => $result], JSON_UNESCAPED_UNICODE);
