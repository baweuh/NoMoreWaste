<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['delivery_id']) && isset($_POST['service_id'])) {
    $_SESSION['delivery_id'] = $_POST['delivery_id'];
    $_SESSION['service_id'] = $_POST['service_id'];
}


if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}