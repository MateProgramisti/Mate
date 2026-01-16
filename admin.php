<?php
session_start();
require_once 'config.php';

$conn = getDBConnection();
$result = $conn->query("SELECT * FROM monitors ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Admin Panel</h1>
    <a href="add-product.php">Add New Product</a>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Product Code</th>
            <th>Name (KA)</th>
            <th>Name (EN)</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['product_code']) ?></td>
            <td><?= htmlspecialchars($row['name_ka']) ?></td>
            <td><?= htmlspecialchars($row['name_en']) ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['stock'] ?></td>
            <td><img src="../images/<?= $row['image'] ?>" width="50"></td>
            <td>
                <a href="edit-product.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="delete-product.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
