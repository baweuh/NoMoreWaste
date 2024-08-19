<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.html");
    exit();
} else if (!isset($_SESSION['role']) || $_SESSION['role'] !== "benevoles") {
    header("Location: ../../index.html");
    exit();
}

?>
