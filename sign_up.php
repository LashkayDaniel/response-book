<?php
session_start();
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Реєстрація</title>
</head>

<body>

<form method="post" action="db/sign_up_controll.php">
    <h1>Реєстрація</h1>
    <p>Логін:</p>
    <input type="text" required name="login" placeholder="Введіть логін">
    <p>Email:</p>
    <input type="email" name="email" placeholder="Email" required>
    <p>Пароль:</p>
    <input type="password" required name="password" placeholder="Введіть пароль">

    <p>Статус:</p>
    <select name="role">
        <option value="user" selected>Користувач</option>
        <option value="admin">Адміністратор</option>
    </select>
    <br/>
    <button type="submit">Зареєструватися</button>
    <p><a href="/">Авторизація</a></p>
    <hr>

    <?php
    if ($_SESSION) {
        if ($_SESSION['user_exist']) {
            echo "<b style='color: red'> user exist</b>";
        } else {
            header("Location: /");
        }
    }
    unset($_SESSION['user_exist']);
    ?></form>
</body>
</html>