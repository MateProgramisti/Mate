<?php
require_once 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $product_id = isset($_POST['product_id']) ? intval(value: $_POST['product_id']) : 0;
        
        if ($action === 'remove') {
            $_SESSION['cart'] = array_filter(array: $_SESSION['cart'], callback: function($item) use ($product_id): bool {
                return $item['id'] != $product_id;
            });
            $_SESSION['cart'] = array_values(array: $_SESSION['cart']);
        } elseif ($action === 'update') {
            $quantity = isset($_POST['quantity']) ? intval(value: $_POST['quantity']) : 1;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product_id) {
                    if ($quantity > 0 && $quantity <= $item['max_stock']) {
                        $item['quantity'] = $quantity;
                    }
                    break;
                }
            }
        }
        header(header: 'Location: cart.php');
        exit;
    }
}

$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.18;
$total = $subtotal + $tax;
?>
<!DOCTYPE html>
<html lang="ka">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>კალათა - MSHOP</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 80px auto 40px;
            padding: 20px;
        }
        .cart-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-table th,
        .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .cart-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .cart-summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-summary-row.total {
            font-size: 1.2em;
            font-weight: bold;
            border-bottom: none;
            color: #2563eb;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
            font-size: 16px;
            padding: 12px 30px;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .checkout-section {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <header>
        <div id="hello" style="background-color: #1e1d1d; position:absolute; top:0; left:0; right:0; height: 40px;">
            <div class="welcome_text" style="font-size:18px; line-height:20px; margin-top: 10px; margin-left: 20px">
                <i class="nomeri" aria-hidden="true"></i>&nbsp; <span>&#9990;+995&nbsp;555 123 456</span>
                &nbsp;&nbsp;
                <i class="maeili" aria-hidden="true"></i>&nbsp; <span>&#128231;mmaghlakelidze@seu.edu.ge</span>
            </div>                    
        </div>
        <div class="navbar">
            <div class="logo">
                <a href="index.php"><p style="margin-left: 40px;">MShop - მონიტორების მაღაზია</p></a> 
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">მთავარი</a></li>
                    <li><a href="about.php">შესახებ</a></li>
                    <li><a href="reg-form.php">რეგისტრაცია</a></li>
                    <li><a href="log-form.php">ავტორიზაცია</a></li>
                    <li><a href="cart.php" class="active">კალათა (<?php echo count(value: $_SESSION['cart']); ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="cart-container">
        <h1>საყიდლების კალათა</h1>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <h2>თქვენი კალათა ცარიელია</h2>
                <p>დაამატეთ პროდუქტები კალათაში</p>
                <br>
                <a href="index.php" class="btn btn-primary">მაღაზიაში დაბრუნება</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>პროდუქტი</th>
                        <th>კოდი</th>
                        <th>ფასი</th>
                        <th>რაოდენობა</th>
                        <th>ჯამი</th>
                        <th>მოქმედება</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div><?php echo htmlspecialchars(string: $item['name']); ?></div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars(string: $item['product_code']); ?></td>
                        <td>$<?php echo number_format(num: $item['price'], decimals: 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="number" 
                                       name="quantity" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" 
                                       max="<?php echo $item['max_stock']; ?>"
                                       class="quantity-input"
                                       onchange="this.form.submit()">
                            </form>
                        </td>
                        <td>$<?php echo number_format(num: $item['price'] * $item['quantity'], decimals: 2); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-danger">წაშლა</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>შუალედური ჯამი:</span>
                    <span>$<?php echo number_format(num: $subtotal, decimals: 2); ?></span>
                </div>
                <div class="cart-summary-row">
                    <span>დღგ (18%):</span>
                    <span>$<?php echo number_format(num: $tax, decimals: 2); ?></span>
                </div>
                <div class="cart-summary-row total">
                    <span>სულ გადასახდელი:</span>
                    <span>$<?php echo number_format(num: $total, decimals: 2); ?></span>
                </div>
            </div>

            <div class="checkout-section">
                <a href="index.php" class="btn">გაგრძელება შოპინგის</a>
                <a href="checkout.php" class="btn btn-primary">შეკვეთის გაფორმება</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>© 17/01/2026 მონიტორების მაღაზია</p>
    </footer>
</body>

</html>
