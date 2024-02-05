<?php

class SessionRequestHandler
{
    public function checkLoginStatus(): bool
    {
        return isset($_SESSION['email']);
    }

    public function login(string $email, string $password): bool
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('SELECT * FROM `users` WHERE email = ?');
        $selectStatement->execute([$email]);

        $user = $selectStatement->fetch();
        if (!$user) {
            return false;
        }

        $loginSuccessful = password_verify($password, $user['password']);

        if ($loginSuccessful) {
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $user['name'];
        }

        return $loginSuccessful;
    }

    public function register(string $email, string $password, string $name): bool
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('SELECT * FROM `users` WHERE email = ?');
        $selectStatement->execute([$email]);

        $user = $selectStatement->fetch();

        if (!$user) {
            $insertStatement = $conn->prepare(
                'INSERT INTO `users` (`email`, `password`, `name`) VALUES(:email, :password, :name)'
            );
            $insertStatement->execute([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'name' => $name
            ]);

            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        session_destroy();
    }

    public function getUserProfile(): array
    {
        $conn = (new Db())->getConnection();

        $result = array();
        $result['name'] = $_SESSION['name'];

        $selectStatement = $conn->prepare('SELECT COUNT(*) FROM `tasks` WHERE user = ?');
        $selectStatement->execute([$_SESSION['name']]);
        $result['task_count'] = $selectStatement->fetchColumn();

        $selectStatement = $conn->prepare('SELECT SUM(time) as sum FROM `tasks` WHERE user = ?');
        $selectStatement->execute([$_SESSION['name']]);
        $result['task_time'] = $selectStatement->fetchColumn();

        $selectStatement = $conn->prepare('SELECT COUNT(*) as sum FROM `project_users` WHERE user = ? AND accepted = FALSE');
        $selectStatement->execute([$_SESSION['name']]);
        $result['invitations'] = $selectStatement->fetchColumn();

        return $result;
    }

    public function getUserProjects(): array
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('SELECT * FROM `projects` WHERE owner = ?');
        $selectStatement->execute([$_SESSION['name']]);

        $projects = $selectStatement->fetchAll();
        return $projects;
    }

    public function setCurrentProject(int $projectId): void
    {
        $_SESSION['project_id'] = $projectId;
        $_SESSION['task_id'] = -1;
    }

    public function unsetCurrentProject(): void
    {
        $_SESSION['project_id'] = -1;
        $_SESSION['task_id'] = -1;
    }

    public function setTask(int $taskId): void
    {
        $_SESSION['task_id'] = $taskId;
    }

    public function deleteTask(int $taskId): bool
    {
        $_SESSION['task_id'] = -1;

        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('DELETE FROM `tasks` WHERE task_id = ?');
        return $selectStatement->execute([$taskId]);
    }

    public function getCurrentProject(): array
    {
        $conn = (new Db())->getConnection();

        $project = array();

        if ($_SESSION['project_id'] != -1) {
            $selectStatement = $conn->prepare('SELECT * FROM `projects` WHERE project_id = ?');
            $selectStatement->execute([$_SESSION['project_id']]);

            $project[] = $selectStatement->fetch();

            $selectStatement = $conn->prepare('SELECT * FROM `tasks` WHERE project_id = ?');
            $selectStatement->execute([$_SESSION['project_id']]);

            $project[] = $selectStatement->fetchAll();
        }

        return $project;
    }

    public function saveProject(string $projectName, string $projectType): bool
    {
        $conn = (new Db())->getConnection();

        if ($_SESSION['project_id'] == -1) {
            $selectStatement = $conn->prepare('INSERT INTO `projects` (name, type, owner) VALUES (?, ?, ?)');
            $result = $selectStatement->execute([$projectName, $projectType, $_SESSION['name']]);

            if ($result) {
                $selectStatement = $conn->prepare('SELECT MAX(project_id) FROM `projects`');
                $result = $selectStatement->execute();
                $_SESSION['project_id'] = $selectStatement->fetchColumn();
            }
        } else {
            $selectStatement = $conn->prepare('UPDATE `projects` SET name = ?, type = ? WHERE project_id = ?');
            $result = $selectStatement->execute([$projectName, $projectType, $_SESSION['project_id']]);
        }

        return $result;
    }

    public function saveTask(string $tasktName, string $taskType, int $taskTime, string $taskUser): bool
    {
        $conn = (new Db())->getConnection();

        if ($_SESSION['project_id'] == -1) {
            $result = false;
        } else {
            if ($_SESSION['task_id'] == -1) {
                $selectStatement = $conn->prepare('INSERT INTO `tasks` (name, type, time, project_id, user) VALUES (?, ?, ?, ?, ?)');
                $result = $selectStatement->execute([$tasktName, $taskType, $taskTime, $_SESSION['project_id'], $taskUser]);
            } else {
                $selectStatement = $conn->prepare('UPDATE `tasks` SET name = ?, type = ?, time = ?, user = ? WHERE task_id = ?');
                $result = $selectStatement->execute([$tasktName, $taskType, $taskTime, $taskUser, $_SESSION['task_id']]);
            }
        }

        return $result;
    }
}
