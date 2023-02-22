<?php
declare(strict_types=1);
session_start();
require_once 'db_func.php';

global $ip, $browser;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $login = $_POST['login'];
        $email = $_POST['email'];
        $password = passHash($_POST['password']);
        $role = $_POST['role'];


        if (findUser($login, $email) == null) {
            switch ($role) {
                case "user":
                    $roleId = getRoleId("Користувач");
                    break;

                case "admin":
                    $roleId = getRoleId("Адмін");
                    break;
            }
            $addUserResult = addNewUser($login, $email, $roleId, $password, $ip, $browser);

            if ($addUserResult) {
                setcookie("login", $login, time() + (86400 * 7), '/'); // 7days
                setcookie("email", $email, time() + (86400 * 7), '/'); // 7days
                if ($roleId == getRoleId("Адмін")) {
                    setcookie("isAdmin", passHash("admin_root"), time() + (86400 * 7), '/');
                }
            }
        } else {
            $_SESSION['user_exist'] = true;
            header("Location: ../sign_up.php");
            return;
        }
        header("Location: /");
    }
} else {
    header("Location: /");
}

