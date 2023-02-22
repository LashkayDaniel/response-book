<?php
declare(strict_types=1);
session_start();
require_once 'db_func.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $login = $_POST['login'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = findUser($login, $email);
        if (!empty($user)) {
            echo $user[0]['password'];
            if (password_verify($password, $user[0]['password'])) {
                // user is checked
                setcookie("login", $login, time() + (86400 * 7), '/'); // 7days
                setcookie("email", $email, time() + (86400 * 7), '/'); // 7days

                $roleId = getRoleId("Адмін");
                if ($user[0]['role_id'] == $roleId) {
                    setcookie("isAdmin", passHash("admin_root"), time() + (86400 * 7), '/');
                }

            } else {
                $_SESSION['password_correct'] = false;
            }
        } else {
            $_SESSION['user_fount'] = false;
        }
        header("Location: /");
    }
} else {
    header("Location: /");
}