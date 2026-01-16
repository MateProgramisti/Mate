<?php
require_once 'config.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$usernameOrEmail || !$password) {
        echo json_encode(['success'=>false, 'message'=>'გთხოვთ შეავსოთ ყველა ველი']);
        exit;
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT id, username, email, password, is_admin FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success'=>false, 'message'=>'მომხმარებელი ვერ მოიძებნა']);
        exit;
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success'=>false, 'message'=>'პაროლი არასწორია']);
        exit;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin'];

    echo json_encode(['success'=>true, 'message'=>'შესვლა წარმატებით მოხდა']);
    
    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['success'=>false, 'message'=>'არასწორი მოთხოვნა']);
}
?>
