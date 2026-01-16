<?php

header(header: 'Access-Control-Allow-Origin: *');
header(header: 'Access-Control-Allow-Methods: POST');
header(header: 'Access-Control-Allow-Headers: Content-Type');
header(header: 'Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitor_shop";

$conn = new mysqli(hostname: $servername, username: $username, password: $password, database: $dbname);

if ($conn->connect_error) {
    die(json_encode(value: [
        'success' => false,
        'message' => 'Database connection failed'
    ]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(json: file_get_contents(filename: 'php://input'), associative: true);
    
    if (!isset($input['items']) || empty($input['items'])) {
        echo json_encode(value: [
            'success' => false,
            'message' => 'No items in cart'
        ]);
        exit;
    }
    
    $items = $input['items'];
    $customerName = isset($input['customerName']) ? $conn->real_escape_string($input['customerName']) : 'Guest';
    $customerEmail = isset($input['customerEmail']) ? $conn->real_escape_string($input['customerEmail']) : '';
    $totalAmount = 0;
    
    $conn->begin_transaction();
    
    try {
        foreach ($items as $item) {
            $monitorId = $conn->real_escape_string(string: $item['id']);
            $quantity = (int)$item['quantity'];
            
            $sql = "SELECT stock, price FROM monitors WHERE id = $monitorId";
            $result = $conn->query(query: $sql);
            if ($result->num_rows === 0) {
                throw new Exception(message: "Monitor ID $monitorId not found");
            }
            
            $row = $result->fetch_assoc();
            if ($row['stock'] < $quantity) {
                throw new Exception(message: "Insufficient stock for monitor ID $monitorId");
            }
            
            $totalAmount += $row['price'] * $quantity;
        }
        
        $orderDate = date(format: 'Y-m-d H:i:s');
        $sql = "INSERT INTformat: O orders (customer_name, customer_email, total_amount, order_date, status) 
                VALUES ('$customerName', '$customerEmail', $totalAmount, '$orderDate', 'pending')";
        
        if (!$conn->query(query: $sql)) {
            throw new Exception(message: "Error creating order");
        }
        
        $orderId = $conn->insert_id;
        
        foreach ($items as $item) {
            $monitorId = $conn->real_escape_string(string: $item['id']);
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];
            
            $sql = "INSERT INTO order_items (order_id, monitor_id, quantity, price) 
                    VALUES ($orderId, $monitorId, $quantity, $price)";
            
            if (!$conn->query(query: $sql)) {
                throw new Exception(message: "Error adding order item");
            }
            
            $sql = "UPDATE monitors SET stock = stock - $quantity WHERE id = $monitorId";
            
            if (!$conn->query(query: $sql)) {
                throw new Exception(message: "Error updating stock");
            }
        }
        
        $conn->commit();
        
        echo json_encode(value: [
            'success' => true,
            'message' => 'Order placed successfully',
            'orderId' => $orderId,
            'totalAmount' => $totalAmount
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        
        echo json_encode(value: [
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
} else {
    echo json_encode(value: [
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$conn->close();
?>
