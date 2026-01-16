<?php
require_once 'config.php';

$search_query = isset($_GET['q']) ? trim(string: $_GET['q']) : '';
$monitors = [];

if (!empty($search_query)) {
    $conn = getDBConnection();
    $search_term = '%' . $conn->real_escape_string(string: $search_query) . '%';
    
    $sql = "SELECT * FROM monitors 
            WHERE (name_ka LIKE ? OR name_en LIKE ? OR product_code LIKE ? OR brand LIKE ?) 
            AND stock > 0 
            ORDER BY name_ka ASC";
    
$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssss",
    $search_term,
    $search_term,
    $search_term,
    $search_term
);

$stmt->execute();
$result = $stmt->get_result();

    
    while($row = $result->fetch_assoc()) {
        $monitors[] = $row;
    }
    
    $stmt->close();
    $conn->close();
}

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ka';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ძებნა: <?php echo htmlspecialchars(string: $search_query); ?> - MSHOP</title>
    <link rel="stylesheet" href="styles.css">
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
                    <li><a href="cart.php">კალათა (<?php echo count($_SESSION['cart']); ?>)</a></li>
                </ul>
            </nav>
            <div class="search">
                <form action="search.php" method="GET">
                    <input type="text" name="q" placeholder="რას ეძებთ?" value="<?php echo htmlspecialchars(string: $search_query); ?>" required>
                    <button type="submit">ძებნა</button>
                </form>
            </div>
        </div>
    </header>
    
    <main>
        <section>
            <center>
                <h2>ძებნის შედეგები: "<?php echo htmlspecialchars(string: $search_query); ?>"</h2>
                <p>ნაპოვნია <?php echo count($monitors); ?> პროდუქტი</p>
            </center>
        </section>
    </main>

    <div class="container">
        <?php if (empty($monitors)): ?>
            <section>
                <center>
                    <p style="padding: 40px; font-size: 18px; color: #666;">
                        პროდუქტები ვერ მოიძებნა. სცადეთ სხვა საძიებო სიტყვა.
                    </p>
                    <a href="index.php" style="padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px;">
                        მთავარ გვერდზე დაბრუნება
                    </a>
                </center>
            </section>
        <?php else: ?>
            <section>
                <div class="category">
                    <?php 
                    $count = 0;
                    foreach ($monitors as $monitor): 
                        $count++;
                        $name = $lang == 'ka' ? $monitor['name_ka'] : $monitor['name_en'];
                    ?>
                    <div class="product">
                        <div class="image-placeholder image-placeholder<?php echo ($count % 8) + 1; ?>">
                            <img src="<?php echo htmlspecialchars(string: $monitor['image']); ?>" 
                                 alt="<?php echo htmlspecialchars(string: $name); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars(string: $monitor['product_code']); ?></h3>
                        <p style="font-size: 14px; color: #666; min-height: 40px;">
                            <?php echo htmlspecialchars(string: $name); ?>
                        </p>
                        <p class="price">$<?php echo number_format(num: $monitor['price'], decimals: 2); ?></p>
                        <p style="font-size: 12px; color: #999;">
                            მარაგში: <?php echo $monitor['stock']; ?> ცალი
                        </p>
                        <div class="productbtns">
                            <button onclick="addToCart(<?php echo $monitor['id']; ?>)">
                                კალათაში დამატება
                            </button>
                            <button onclick="buyNow(<?php echo $monitor['id']; ?>)">
                                ყიდვა
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <footer>
        <p>© 17/01/2026 მონიტორების მაღაზია</p>
    </footer>

    <script>
        function addToCart(productId) {
            fetch('add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('პროდუქტი დაემატა კალათაში!');
                    location.reload();
                } else {
                    alert('შეცდომა: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('დაფიქსირდა შეცდომა!');
            });
        }

        function buyNow(productId) {
            fetch('add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'checkout.php';
                } else {
                    alert('შეცდომა: ' + data.message);
                }
            });
        }
    </script>
</body>

</html>
