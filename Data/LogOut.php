<?php
    setcookie("NameUserM", "", time() - 3600, "/");
    setcookie("UserNameP", "", time() - 3600, "/");
    setcookie("RoleDB", "", time() - 3600, "/");
    setcookie("UserCC", "", time() - 3600, "/");

    header("Location: ../Index.php");
    exit();
?>