<?php

class SessionRequestHandler
{

    public function getUserName(): string
    {
        return $_SESSION['name'];
    }

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
    }

    public function unsetCurrentProject(): void
    {
        $_SESSION['project_id'] = -1;
    }

    public function getCurrentProject(): array
    {
        $conn = (new Db())->getConnection();

        $project = array();

        if ($_SESSION['project_id'] != -1) {
            $selectStatement = $conn->prepare('SELECT * FROM `projects` WHERE project_id = ?');
            $selectStatement->execute([$_SESSION['project_id']]);

            $project = $selectStatement->fetch();
        }

        return $project;
    }

    public function saveProject(string $projectName, string $projectType): bool
    {
        $conn = (new Db())->getConnection();

        if ($_SESSION['project_id'] == -1) {
            $selectStatement = $conn->prepare('INSERT INTO `projects` (name, type, owner) VALUES (?, ?, ?)');
            $result = $selectStatement->execute([$projectName, $projectType, $_SESSION['name']]);
        } else {
            $selectStatement = $conn->prepare('UPDATE `projects` SET name = ?, type = ? WHERE project_id = ?');
            $result = $selectStatement->execute([$projectName, $projectType, $_SESSION['project_id']]);
        }

        return $result;
    }
}
