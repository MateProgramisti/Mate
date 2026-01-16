<?php
session_start();
require_once 'config.php';

$id = intval($_GET['id']);
$conn = getDBConnection();
$stmt = $conn->prepare("DELETE FROM monitors WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: admin.php');
