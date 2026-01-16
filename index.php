<?php
require_once 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$conn = getDBConnection();
$sql = "SELECT * FROM monitors WHERE stock > 0 ORDER BY id ASC";
$result = $conn->query(query: $sql);

$monitors = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $monitors[] = $row;
    }
}

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ka';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSHOP - მონიტორების მაღაზია</title>
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
                    <li><a href="about.html">შესახებ</a></li>
                    <li><a href="reg-form.html">რეგისტრაცია</a></li>
                    <li><a href="log-form.html">ავტორიზაცია</a></li>
                    <li><a href="cart.php">კალათა (<?php echo count(value: $_SESSION['cart']); ?>)</a></li>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <li><a href="admin.php" style="color:red;">Admin Panel</a></li>
        <?php endif; ?>
                </ul>
            </nav>
            <div class="search">
                <form action="search.php" method="GET">
                    <input type="text" name="q" placeholder="რას ეძებთ?" required>
                    <button type="submit">ძებნა</button>
                </form>
            </div>
        </div>
    </header>
    
    <main>
        <section>
            <center><h2 id="PCs">მონიტორები</h2></center>
        </section>
    </main>

    <select id="languageSelector" onchange="changeLanguage(this.value)">
        <option value="ka" <?php echo $lang == 'ka' ? 'selected' : ''; ?>>ქართული</option>
        <option value="en" <?php echo $lang == 'en' ? 'selected' : ''; ?>>English</option>
    </select>

<div class="container">
  <section>
    <div class="category">
      <?php foreach ($monitors as $count => $monitor): 
        $name = $lang == 'ka' ? $monitor['name_ka'] : $monitor['name_en'];
      ?>
      <div class="product">
        <div class="image-placeholder image-placeholder<?php echo ($count+1); ?>">
          <img src="<?php echo htmlspecialchars($monitor['image']); ?>" alt="<?php echo htmlspecialchars($name); ?>">
        </div>
        <h3><?php echo htmlspecialchars($monitor['product_code']); ?></h3>
        <p style="font-size: 14px; color: #666; min-height: 40px;"><?php echo htmlspecialchars($name); ?></p>
        <p class="price">$<?php echo number_format($monitor['price'], 2); ?></p>
        <p style="font-size: 12px; color: #999;">მარაგში: <?php echo $monitor['stock']; ?> ცალი</p>
        <div class="productbtns">
          <button onclick="addToCart(<?php echo $monitor['id']; ?>)">კალათაში დამატება</button>
          <button onclick="buyNow(<?php echo $monitor['id']; ?>)">ყიდვა</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

    </section>
</div>


    <footer>
        <p>© 17/01/2026 მონიტორების მაღაზია</p>
    </footer>

    <script>
        function changeLanguage(lang) {
            window.location.href = '?lang=' + lang;
        }

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
    <script src="script.js"></script>
</body>

</html>
<?php
$conn->close();
?>
