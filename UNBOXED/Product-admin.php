<?php
session_start(); // Start session to manage user login state

// Connect to your database
$servername = "localhost";
$username = "root"; // Default MySQL username for XAMPP
$password = ""; // Default MySQL password for XAMPP
$dbname = "unboxed"; // Replace 'your_database' with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$query = "SELECT * FROM product";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products</title>
  <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
</head>
<body>
  <div class="sidebar">
    <div class="nav-top">

    </div>
        <a href="#">
            <i class="fa fa-user fa-4x" aria-hidden="true"></i>
        </a>
        <h2>Admin</h2>
        <ul class="nav">
            <li>
                <a href="admin.php">
                <i class="bi bi-speedometer"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="Product-admin.php">
                <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="Orders-admin.php">
                <i class="bi bi-list-ul"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="users.php">
                <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>
            </li>
            <li>
            <a href="login.php" onclick="return confirm('Are you sure you want to logout?')">
            <i class="bi bi-box-arrow-left"></i>
          <span>Logout</span>
                </a>
            </li>
        </ul>

    </div>
    <div class="product-main">
    <h1>PRODUCTS</h1>
    <div class="header-order">
      <p class="prod-img">Product Image</p>
      <p class="prod-name">Product Name</p>
      <p class="prod-price">Price</p>
      <p class="prod-stock">Stocks</p>
      <p class="prod-action">Action</p>
    </div>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <div class="prod-con">
        <img src="<?php echo $row['product_img']; ?>" alt="<?php echo $row['name']; ?>" class="product-image">
        <p class="product-name"><?php echo $row['name']; ?></p>
        <p class="product-price">₱<?php echo number_format($row['price'], 2); ?></p>  
        <p class="product-stock"><?php echo $row['stocks']; ?></p>
        <p class="product-action">
          <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
        </p>
      </div>
    <?php } ?>
  </div>
</body>
</html>
