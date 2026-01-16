<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'არასწორი მოთხოვნა'
    ]);
    exit;
}

if (!isset($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'პროდუქტის ID არ მოიძებნა'
    ]);
    exit;
}

$product_id = (int) $_POST['product_id'];

if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'არასწორი პროდუქტის ID'
    ]);
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare(
    "SELECT id, product_code, name_ka, price, stock 
     FROM monitors 
     WHERE id = ? AND stock > 0"
);

$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'პროდუქტი არ მოიძებნა ან არ არის მარაგში'
    ]);
    exit;
}

$product = $result->fetch_assoc();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;

foreach ($_SESSION['cart'] as &$item) {
    if (isset($item['id']) && $item['id'] === $product_id) {
        if ($item['quantity'] < $product['stock']) {
            $item['quantity']++;
            $found = true;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'მარაგი არ არის საკმარისი'
            ]);
            exit;
        }
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $product['id'],
        'product_code' => $product['product_code'],
        'name' => $product['name_ka'],
        'price' => $product['price'],
        'quantity' => 1,
        'max_stock' => $product['stock']
    ];
}

echo json_encode([
    'success' => true,
    'message' => 'პროდუქტი წარმატებით დაემატა კალათაში',
    'cart_count' => count($_SESSION['cart'])
]);

$stmt->close();
$conn->close();
exit;
