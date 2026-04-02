<?php session_start();
require_once __DIR__ . '/../includes/config.php';
session_destroy();
header('Location: ' . $BASE . '/admin/login.php'); exit;
