<?php
setcookie("login", "", time() - 3600, '/');
setcookie("email", "", time() - 3600, '/');
setcookie("isAdmin", "", time() - 3600, '/');
header("Location: /");