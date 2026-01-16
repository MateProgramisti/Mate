<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request method']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$city = trim($_POST['city'] ?? '');
$country = trim($_POST['country'] ?? '');
$password = $_POST['password'] ?? '';
$registrationDate = $_POST['registrationDate'] ?? '';

if (!$username || !$email || !$password || !$mobile || !$city || !$country || !$registrationDate) {
    echo json_encode(['success'=>false,'message'=>'ყველა ველი აუცილებელია']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'ელ. ფოსტა უკვე არსებობს']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO users (username, email, mobile, city, country, password, registrationDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $username, $email, $mobile, $city, $country, $hashedPassword, $registrationDate);

if ($stmt->execute()) {
    echo json_encode(['success'=>true,'message'=>'რეგისტრაცია წარმატებით დასრულდა']);
} else {
    echo json_encode(['success'=>false,'message'=>'დაფიქსირდა შეცდომა მონაცემთა ბაზაში']);
}

$stmt->close();
$conn->close();
