<?php
session_start();
require_once 'db/db_func.php';
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js" defer></script>
    <title>Адмін-панель</title>
</head>

<body>
<?php
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$limit = 10;
$offset = ($page - 1) * $limit;
$result = pagination($offset, $limit);

$count = getUsersCount();
$totalPage = ceil($count / $limit);
?>

<table>
    <thead class="table-info">
    <th>Id</th>
    <th>Username</th>
    <th>Email</th>
    <th>Type</th>
    <th>Response</th>
    <th>Image</th>
    <th>Txt</th>
    <th>Created At</th>
    </thead>
    <tbody>
    <?php
    foreach ($result as $data) {
        echo '<tr>';
        echo '<td>' . $data['id'] . '</td>';
        echo '<td>' . $data['username'] . '</td>';
        echo '<td>' . $data['email'] . '</td>';
        echo '<td>' . $data['type'] . '</td>';
        echo '<td>' . $data['response'] . '</td>';
        $image = $data['image'];
        if (!empty($image)) {
            $src = 'data:image;base64,' . base64_encode($image);
            echo '<td>' . '<img class="modal-hover-opacity" onclick="onClick(this)" src="' . $src . '" width="60px" height="40px" alt="Image">' . '</td>';
        } else {
            echo '<td>' . '' . '</td>';
        }
        echo '<td>' . $data['txt'] . '</td>';
        echo '<td>' . $data['created_at'] . '</td>';
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<div class="pagination">
    <?php
    for ($j = 1;
         $j <= $totalPage;
         $j++) {
        echo "<a href=?page=$j>" . $j . "</a>";
    }
    ?>
</div>

<div id="modal01" class="modal" onclick="this.style.display='none'">
    <span class="close">&times;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <div class="modal-content">
        <img id="img01" style="max-width:100%">
    </div>
</div>
</body>
</html>