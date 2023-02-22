<?php
declare(strict_types=1);
session_start();
require_once 'db_func.php';

global $ip, $browser;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $response = $_POST['response'];

        $fileType = $_FILES['file']['type'];

        if (checkImage($fileType)) {

            $imgName = $_FILES['file']['tmp_name'];

            list($width, $height) = getimagesize($imgName);
            $type = mime_content_type($imgName);
            $maxWidth = 800;
            $maxHeight = 600;

            if ($width > $maxWidth) {
                $koef = $width / $maxWidth;
                $newHeight = (int)ceil($height / $koef);
                $newImg = imagecreatetruecolor($maxWidth, $newHeight);
                switch ($type) {
                    case 'image/png':
                        $src = imagecreatefrompng($imgName);
                        break;
                    case 'image/jpg':
                    case 'image/jpeg':
                        $src = imagecreatefromjpeg($imgName);
                        break;
                }

                imagecopyresampled($newImg, $src, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);

                ob_start();
                switch ($type) {
                    case 'image/png':
                        imagepng($newImg, null, -1);
                        break;
                    case 'image/jpg':
                    case 'image/jpeg':
                        imagejpeg($newImg, null, -1);
                        break;
                }
                $imageData = ob_get_clean();
                imagedestroy($newImg);

            } else {
                $imageData = file_get_contents($imgName);
            }

            addUserAndResponse($username, $email, $response, $imageData, "", $ip, $browser);
            $_SESSION['file_format_error'] = false;


        } elseif (checkTxt($fileType)) {

            echo "<h1>" . "its txt" . "</h1>";
            $txtName = $_FILES['file']['tmp_name'];
            $txtData = file_get_contents($txtName);
            addUserAndResponse($username, $email, $response, "", $txtData, $ip, $browser);
            $_SESSION['file_format_error'] = false;
        } elseif (!empty($fileType)) {
            $_SESSION['file_format_error'] = true;
        } else {
            addUserAndResponse($username, $email, $response, "", "", $ip, $browser);
            $_SESSION['file_format_error'] = false;
        }
        header("Location: /");
    }
} else {
    header("Location: /");
}

function addUserAndResponse(
    string $username,
    string $email,
    string $response,
    string $imageBlob,
    string $txt,
    string $ip,
    string $browser
): void {
    if (findUser($username, $email) == null) {
        $roleId = getRoleId("Гість");
        addNewUser($username, $email, $roleId, "", $ip, $browser);
    }
    $currentUser = findUser($username, $email);
    if (!is_null($currentUser)) {
        $currentUserId = (int)$currentUser[0]['id'];
        addResponse($currentUserId, $response, $imageBlob, $txt, getCurrentDateTime());
    }
}





