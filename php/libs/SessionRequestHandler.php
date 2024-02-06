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

        $selectStatement = $conn->prepare('SELECT COUNT(*) as count FROM `project_users` WHERE user = ? AND accepted = FALSE');
        $selectStatement->execute([$_SESSION['name']]);
        $result['invitations'] = $selectStatement->fetchColumn();

        return $result;
    }

    public function getUserInvitations(): array
    {
        $conn = (new Db())->getConnection();

        $result = array();

        $selectStatement = $conn->prepare('SELECT * FROM `project_users` WHERE user = ? AND accepted = FALSE');
        $selectStatement->execute([$_SESSION['name']]);
        $projects = $selectStatement->fetchAll();

        foreach ($projects as $project) {
            $selectStatement = $conn->prepare('SELECT * FROM `projects` WHERE project_id = ?');
            $selectStatement->execute([$project['project_id']]);
            $result[] = $selectStatement->fetch();
        }

        return $result;
    }

    public function acceptInvitation(int $projectId): bool
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('UPDATE `project_users` SET accepted = TRUE WHERE project_id = ? AND user = ?');
        return $selectStatement->execute([$projectId, $_SESSION['name']]);
    }

    public function denyInvitation(int $projectId): bool
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('DELETE FROM `project_users` WHERE project_id = ? AND user = ?');
        $selectStatement->execute([$projectId, $_SESSION['name']]);

        $selectStatement = $conn->prepare('DELETE FROM `tasks` WHERE project_id = ? AND user = ?');
        return $selectStatement->execute([$projectId, $_SESSION['name']]);
    }

    public function getUserProjects(): array
    {
        $conn = (new Db())->getConnection();

        $result = array();

        $selectStatement = $conn->prepare('SELECT * FROM `project_users` WHERE user = ? AND accepted = TRUE');
        $selectStatement->execute([$_SESSION['name']]);
        $projects = $selectStatement->fetchAll();

        foreach ($projects as $project) {
            $selectStatement = $conn->prepare('SELECT * FROM `projects` WHERE project_id = ?');
            $selectStatement->execute([$project['project_id']]);
            $received = $selectStatement->fetch();
            $received['not_owned'] = (bool)strcmp($_SESSION['name'], $received['owner']);
            $result[] = $received;
        }

        return $result;
    }

    public function getPresets(): array
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('SELECT DISTINCT type FROM `projects`');
        $selectStatement->execute();
        return $selectStatement->fetchAll();
    }

    public function getTaskTypes(): array
    {
        $conn = (new Db())->getConnection();

        if ($_SESSION['project_id'] == -1) {
            return array();
        }

        $selectStatement = $conn->prepare('SELECT DISTINCT type FROM `tasks` WHERE project_id IN ' .
            '(SELECT project_id FROM `projects` WHERE type = (SELECT type from `projects` WHERE project_id = ?))');
        $selectStatement->execute([$_SESSION['project_id']]);
        return $selectStatement->fetchAll();
    }

    public function getProjectUsers(): array
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('SELECT DISTINCT user FROM `project_users` WHERE project_id = ?');
        $selectStatement->execute([$_SESSION['project_id']]);
        return $selectStatement->fetchAll();
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

    public function deleteProject(int $projectId): bool
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('DELETE FROM `projects` WHERE project_id = ?');
        $selectStatement->execute([$projectId]);

        $selectStatement = $conn->prepare('DELETE FROM `project_users` WHERE project_id = ?');
        $selectStatement->execute([$projectId]);

        $selectStatement = $conn->prepare('DELETE FROM `tasks` WHERE project_id = ?');
        return $selectStatement->execute([$projectId]);
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
                $selectStatement->execute();
                $_SESSION['project_id'] = $selectStatement->fetchColumn();

                $selectStatement = $conn->prepare('INSERT INTO `project_users` (user, project_id, accepted) VALUES (?, ?, TRUE)');
                $result = $selectStatement->execute([$_SESSION['name'], $_SESSION['project_id']]);
            }
        } else {
            $selectStatement = $conn->prepare('UPDATE `projects` SET name = ?, type = ? WHERE project_id = ?');
            $result = $selectStatement->execute([$projectName, $projectType, $_SESSION['project_id']]);
        }

        return $result;
    }

    public function saveAutomaticTask(string $taskName, string $taskType, string $taskUser): bool
    {
        $conn = (new Db())->getConnection();

        $selectStatement = $conn->prepare('SELECT AVG(time) FROM `task_history` WHERE type = ?');
        $selectStatement->execute([$taskType]);
        $timeNeeded = $selectStatement->fetchColumn();

        if (is_null($timeNeeded)) {
            $timeNeeded = 0;
        }

        return $this->saveTask($taskName, $taskType, $timeNeeded, $taskUser, true);
    }

    public function saveTask(string $tasktName, string $taskType, int $taskTime, string $taskUser, bool $isAutomatic): bool
    {
        $conn = (new Db())->getConnection();

        if ($_SESSION['project_id'] == -1) {
            $result = false;
        } else {
            if ($_SESSION['task_id'] == -1) {
                $selectStatement = $conn->prepare('INSERT INTO `tasks` (name, type, time, project_id, user) VALUES (?, ?, ?, ?, ?)');
                $result = $selectStatement->execute([$tasktName, $taskType, $taskTime, $_SESSION['project_id'], $taskUser]);

                if (!$isAutomatic) {
                    $selectStatement = $conn->prepare('INSERT INTO `task_history` (type, time) VALUES (?, ?)');
                    $selectStatement->execute([$taskType, $taskTime]);
                }
            } else {
                $selectStatement = $conn->prepare('UPDATE `tasks` SET name = ?, type = ?, time = ?, user = ? WHERE task_id = ?');
                $result = $selectStatement->execute([$tasktName, $taskType, $taskTime, $taskUser, $_SESSION['task_id']]);
            }

            if ($result) {
                $selectStatement = $conn->prepare('SELECT EXISTS (SELECT * FROM `project_users` WHERE user = ? AND project_id = ?)');
                $selectStatement->execute([$taskUser, $_SESSION['project_id']]);
                $user_exists_in_project = $selectStatement->fetchColumn();

                if ($user_exists_in_project == 0) {
                    if (strcmp($_SESSION['name'], $taskUser)) {
                        $accepted = 0;
                    } else {
                        $accepted = 1;
                    }

                    $selectStatement = $conn->prepare('INSERT INTO `project_users` (user, project_id, accepted) VALUES (?, ?, ?)');
                    $result = $selectStatement->execute([$taskUser, $_SESSION['project_id'], $accepted]);
                }
            }
        }

        return $result;
    }
}
