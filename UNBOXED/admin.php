<?php
session_start();

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
// Fetch product data
$sql = "SELECT name, sold, stocks FROM product";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Calculate the sold rate
        $sold_rate = $row['stocks'] > 0 ? ($row['sold'] / $row['stocks']) * 100 : 0;
        $products[] = [
            'name' => $row['name'],
            'sold_rate' => $sold_rate
        ];
    }
}

// Fetch counts
$productCount = $conn->query("SELECT COUNT(*) as count FROM product")->fetch_assoc()['count'];
$orderCount = $conn->query("SELECT COUNT(*) as count FROM order_item")->fetch_assoc()['count'];
$customerCount = $conn->query("SELECT COUNT(*) as count FROM customer")->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
  <div class="dash-main">
    <h1>DASHBOARD</h1>

    <div class="emps-card">
      <i class="bi bi-people" aria-hidden="true"></i>
      <span class="number"><?php echo $customerCount; ?></span>
      <div class="inside-box">
        <span class="label">Users</span>
        <div class="icon-container">
          <i class="fa fa-check-circle" aria-hidden="true"></i>   
        </div>
      </div>
    </div>

    <div class="dept-card">
      <i class="bi bi-box-seam" aria-hidden="true"></i>
      <span class="number"><?php echo $productCount; ?></span>
      <div class="inside-box">
        <span class="label">Products</span>
        <div class="icon-container">
          <i class="fa fa-check-circle" aria-hidden="true"></i>   
        </div>
      </div>
    </div>

    <div class="shift-card">
      <i class="bi bi-list-ul" aria-hidden="true"></i>
      <span class="number"><?php echo $orderCount; ?></span>
      <div class="inside-box">
        <span class="label">Orders</span>
        <div class="icon-container">
          <i class="fa fa-check-circle" aria-hidden="true"></i>   
        </div>
      </div>
    </div>
    </div>
        <h1 class="sold-rate">Sold Rate</h1>
    <div class="chart-container">
            <!-- Add a canvas element for the chart -->
      <canvas id="soldRateChart"></canvas>
    </div>
  <script>
    // Pass PHP data to JavaScript
    const products = <?php echo json_encode($products); ?>;

    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('soldRateChart').getContext('2d');
      const productNames = products.map(product => product.name);
      const soldRates = products.map(product => product.sold_rate);

      const soldRateChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: productNames,
          datasets: [{
            label: 'Sold Rate (%)',
            data: soldRates,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              max: 100
            }
          }
        }
      });
    });
  </script>
</body>
</html>