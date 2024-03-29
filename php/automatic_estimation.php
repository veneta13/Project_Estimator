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
        if (isset($_POST['project_name'])) {
            $result = (new SessionRequestHandler())->saveProject($_POST['project_name'], $_POST['preset']);
        } else {
            $result = (new SessionRequestHandler())->saveAutomaticTask(
                $_POST['task_name'],
                $_POST['task_type'],
                $_POST['task_user']
            );
        }
        (new SessionRequestHandler())->setTask(-1);

        break;
    }

    case 'PUT':
    {
        $id = json_encode(file_get_contents("php://input"), true);
        $id = substr($id, 1, -1);
        $id = (int)$id;
        (new SessionRequestHandler())->setTask($id);
        break;
    }

    case 'DELETE':
    {
        $id = json_encode(file_get_contents("php://input"), true);
        $id = substr($id, 1, -1);
        $id = (int)$id;
        $result = (new SessionRequestHandler())->deleteTask($id);
        break;
    }

    default:
    {
        $result = "Unknown";
        break;
    }
}

echo json_encode(['result' => $result], JSON_UNESCAPED_UNICODE);
