<?php
declare(strict_types=1);


//--------------------- variables

$ip = $_SERVER['REMOTE_ADDR'];
$browser = $_SERVER['HTTP_USER_AGENT'];

// -------------------- functions
function getConn(): ?PDO
{
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbName = "guest_book";

    try {
        $conn = new PDO("mysql:host=$server;dbname=$dbName", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "<br>" . $e->getMessage();
        return null;
    }
}

function getUsers()
{
    $sql = "select * from users";
    try {
        $con = getConn();
        $users = $con->query($sql);
        $con = null;
    } catch (PDOException $exception) {
        die("error in query: " . $sql . $exception->getMessage());
    }
    return $users ?? null;
}

function addNewUser(string $username, string $email, int $roleId, string $password, string $ip, string $browser): bool
{
    $result = false;
    $insertUserSql = "INSERT INTO users(username,email, role_id, password,ip, browser) VALUES(:username,:email,:roleId,:password,:ip,:browser)";
    try {
        $con = getConn();
        $prepare = $con->prepare($insertUserSql);
        $prepare->bindParam(':username', $username);
        $prepare->bindParam(':email', $email);
        $prepare->bindParam(':roleId', $roleId, PDO::PARAM_INT);
        $prepare->bindParam(':password', $password);
        $prepare->bindParam(':ip', $ip);
        $prepare->bindParam(':browser', $browser);

        $prepare->execute();
        $result = true;
    } catch (PDOException $exception) {
        die("error in query: " . $insertUserSql . $exception->getMessage());
        $result = false;
    }
    unset($con);
    unset($prepare);
    return $result;
}

function findUser(string $username, string $email): ?array
{
    $findUserSql = "SELECT * FROM users WHERE users.username = :username AND users.email = :email";
    try {
        $con = getConn();
        $prepare = $con->prepare($findUserSql);
        $prepare->bindParam(':username', $username);
        $prepare->bindParam(':email', $email);

        $prepare->execute();
        $users = $prepare->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        die("error in query: " . $findUserSql . $exception->getMessage());
    }
    unset($con);
    unset($prepare);
    return $users;
}

function getCurrentDateTime(): string
{
    return date("Y-m-d H:i:s");
}

function addResponse(int $userId, string $response, string $imageBlob, string $txt, string $createdAt): bool
{
    $addResponseSql = "INSERT INTO responses(user_id,response,image,txt,created_at) VALUES(:user_id,:response,:imageBlob,:txt,:created_at)";
    try {
        $con = getConn();
        $prepare = $con->prepare($addResponseSql);
        $prepare->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $prepare->bindParam(':response', $response, PDO::PARAM_STR);
        $prepare->bindParam(':imageBlob', $imageBlob, PDO::PARAM_STR);
        $prepare->bindParam(':txt', $txt, PDO::PARAM_STR);
        $prepare->bindParam(':created_at', $createdAt);

        $prepare->execute();
        $result = true;
    } catch (PDOException $exception) {
        die("error in query: " . $addResponseSql . $exception->getMessage());
        $result = false;
    }
    unset($con);
    unset($prepare);
    return $result;
}

function getRoleId(string $type): int
{
    $getRoleIdSql = "SELECT id FROM roles WHERE type = :type";
    try {
        $con = getConn();
        $prepare = $con->prepare($getRoleIdSql);
        $prepare->bindParam(':type', $type);

        $prepare->execute();
        $role = $prepare->fetch();
    } catch (PDOException $exception) {
        die("error in query: " . $getRoleIdSql . $exception->getMessage());
    }
    unset($con);
    unset($prepare);
    return (int)$role['id'];
}

function passHash(string $password)
{
    $options = [
        'cost' => 12,
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}

function checkImage(string $fileName): bool
{
    $imageExtensions = array('image/jpeg', 'image/jpg', 'image/png');
    return in_array($fileName, $imageExtensions);
}

function checkTxt(string $fileName): bool
{
    return $fileName == "text/plain";
}

function checkUser(string $login, string $email, string $hashPassword): bool
{
    $checkUserSql = "SELECT * FROM users WHERE username = :username AND email=:email AND password=:hashPassword";
    try {
        $con = getConn();
        $prepare = $con->prepare($checkUserSql);
        $prepare->bindParam(':username', $login);
        $prepare->bindParam(':email', $email);
        $prepare->bindParam(':hashPassword', $hashPassword);

        $prepare->execute();
        $count = count($prepare->fetchAll());
    } catch (PDOException $exception) {
        die("error in query: " . $checkUserSql . $exception->getMessage());
    }
    unset($con);
    unset($prepare);
    return $count > 0;
}

//// test  ////
function getUserImg(int $userId): ?array
{
    $getImgSql = "SELECT image FROM responses WHERE user_id=:userId";
    try {
        $con = getConn();
        $prepare = $con->prepare($getImgSql);
        $prepare->bindParam(':userId', $userId);

        $prepare->execute();
        $result = $prepare->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        die("error in query: " . $getImgSql . $exception->getMessage());
    }
    unset($con);
    unset($prepare);
    return $result;
}


function pagination($start, $limit)
{
    $sql = "SELECT responses.id,username,email,roles.type, response, image, txt, created_at FROM users JOIN roles ON users.role_id=roles.id JOIN responses ON users.id = responses.user_id ORDER BY responses.id DESC LIMIT $start, $limit";
    try {
        $con = getConn();
        $prepare = $con->prepare($sql);

        $prepare->execute();
        $result = $prepare->rowCount() > 0 ? $prepare->fetchAll(PDO::FETCH_ASSOC) : 0;
    } catch (PDOException $exception) {
        die("error in query: " . $sql . $exception->getMessage());
    }
    unset($con);
    unset($prepare);
    return $result;
}

function getUsersCount(): int
{
    $sql = "SELECT responses.id,username,email,roles.type, response, image, txt, created_at FROM users JOIN roles ON users.role_id=roles.id JOIN responses ON users.id = responses.user_id";
    try {
        $con = getConn();
        $prepare = $con->prepare($sql);

        $prepare->execute();
        $result = $prepare->rowCount();
    } catch (PDOException $exception) {
        die("error in query: " . $sql . $exception->getMessage());
    }
    unset($con);
    unset($prepare);
    return $result;
}
/// //