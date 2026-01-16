<?php
session_start();
require_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['product_code'];
    $name_ka = $_POST['name_ka'];
    $name_en = $_POST['name_en'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image']['name'] ?? 'default.png';

    if (!empty($_FILES['image']['tmp_name'])) {
        move_uploaded_file($_FILES['image']['tmp_name'], "../images/".$image);
    }

    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO monitors (product_code, name_ka, name_en, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdis", $code, $name_ka, $name_en, $price, $stock, $image);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header('Location: admin.php');
}
?>
<form method="POST" enctype="multipart/form-data">
    Product Code: <input type="text" name="product_code" required><br>
    Name (KA): <input type="text" name="name_ka" required><br>
    Name (EN): <input type="text" name="name_en" required><br>
    Price: <input type="number" step="0.01" name="price" required><br>
    Stock: <input type="number" name="stock" required><br>
    Image: <input type="file" name="image"><br>
    <button type="submit">Add Product</button>
</form>
