<?php
session_start();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Книга відгуків</title>
</head>
<body>

<?php
if (!isset($_COOKIE['login'])) {
    echo '<form method="post" action="./db/sign_in_controll.php">';
    echo <<<EOT
    <h1>Авторизація</h1>
    <p>Логін:</p>
    <input type="text" required name="login" placeholder="Введіть логін">
    <p>Email:</p>
    <input type="email" name="email" placeholder="Email" required>
    <p>Пароль:</p>
    <input type="password" required name="password" placeholder="Введіть пароль">
    <p>
        <button type="submit">Увійти</button>
    </p>
    <p><a href="sign_up.php">Реєстрація</a></p>
    <hr>
EOT;
    if (isset($_SESSION['user_fount']) && !$_SESSION['user_fount']) {
        echo "<b style='color: red'> Неправильний логін або емейл.</b>";
    }
    if (isset($_SESSION['password_correct']) && !$_SESSION['password_correct']) {
        echo "<b style='color: red'> Неправильний пароль.</b>";
    }
    unset($_SESSION['user_fount']);
    unset($_SESSION['password_correct']);

    echo '</form>';
}
?>

<!-- user info -->

<?php
if (isset($_COOKIE['login'])) {
    echo '<form action="logout.php">';
    echo '<h1>User info</h1>';
    echo '<p>' . 'Login : ' . $_COOKIE['login'] . '</p>';
    echo '<p>' . 'Email : ' . $_COOKIE['email'] . '</p>';
    echo '<button type="submit">Logout</button>';
    if (isset($_COOKIE['isAdmin']) && password_verify("admin_root", $_COOKIE['isAdmin'])) {
        echo '<button><a href="admin.php" target="_blank" style="color: #e0aa58; text-decoration: none;">ADMIN PANEL</a></button>';
    }
    echo '</form>';
}
?>

<!--  response form  -->
<form action="db/response.php" method="post" enctype="multipart/form-data">
    <h1>Книга відгуків</h1>

    <p>Ім'я користувача:</p>
    <input type="text" name="username" value="<?php echo isset($_COOKIE['login']) ? $_COOKIE['login'] : ''; ?>"
           placeholder="Введіть ім'я" required>
    <p>Email:</p>
    <input type="email" name="email" value="<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>"
           placeholder="Email" required>
    <p>Image or Txt:</p>
    <input type="file" name="file" accept=".jpeg, .jpg, .png, .txt" placeholder="Виберіть файл">
    <?php
    if (isset($_SESSION['file_format_error']) && $_SESSION['file_format_error']) {
        echo "<b style='color: red'> Неправильний формат, виберіть jpeg, jpg, png або txt.</b>";
    }
    ?>
    <p>Відгук:</p>
    <textarea name="response" placeholder="Напишіть відгук" required></textarea>
    <?php
    if (isset($_SESSION['file_format_error']) && !$_SESSION['file_format_error']) {
        echo "<b style='color: greenyellow'> Відгук успішно надіслано!</b>";
    }
    unset($_SESSION['file_format_error']);
    ?>
    <button type="submit">Надіслати</button>
</form>
</body>
</html>