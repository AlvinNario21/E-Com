<?php
session_start(); // Start session to manage user login state


// Check if the logout button is clicked
if (isset($_GET['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session 
    session_destroy();

    // Redirect to the login page
    header("Location: index.php");
    exit;
}// Start session to manage user login state

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

// Check if the user is logged in
if (isset($_SESSION["email"])) {
    // User is logged in, fetch the user's name and image_dp from the session
    $name = $_SESSION["name"]; // Assuming 'name' is the column name for user's name
    $image_dp = $_SESSION["image_dp"]; // Assuming 'image_dp' is the column name for user's image
// Fetch the wallet information from the database
$sql = "SELECT wallet FROM customer WHERE email = '".$_SESSION["email"]."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $wallet = $row["wallet"];
    }
} else {
    $wallet = 0; // Set default wallet value
}
} else {
// User is not logged in, set default values
$name = "";
$image_dp = "";
$wallet = 0;
}

$contact_num = $_GET["contact_num"];

// Check if the session variable is set and true (meaning "Open the Boxes" button is clicked)
$displayUserInfo = !(isset($_SESSION['open_boxes']) && $_SESSION['open_boxes']);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNBOXED</title>
    <!-- Option 1: Include in HTML -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="nav-logo">
            <a class="navbar-brand" href="Home.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
            <a class="navbar-brand">|</a>
        <a class="navbar-brand">My Wallet</a>
        </div>
        <div class="navbar-nav" id="navbarLinks">
            <a class="nav-link" href="About.php">ABOUT</a>
            <a class="nav-link" href="FAQ.php">FAQ</a>
            <?php if ($displayUserInfo && !empty($name)): ?>
                <!-- Display user's name and image_dp -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="Profile.php?customer_id=<?php echo urlencode($customer_id); ?>&name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Profile</a>
                    <a class="dropdown-item" href="Purchase.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Purchase</a>
                    <a class="dropdown-item" href="Orders.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">Orders</a>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?logout=1">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
               <!-- If user is not logged in or "Open the Boxes" button is clicked, display Sign Up and Login links -->
                <a class="nav-link" href="Create-Box.php">Sign Up</a>
                <a class="nav-link">|</a>
                <a class="nav-link" href="login.php">Login</a>
            <?php endif; ?>

        </div>
    </div>
</nav>

<div class="wallet-container">
    <p class="bal">Balance Overview</p>
    <div class="balance">
        <p class="bal">Seller Balance</p>
        <div class="vertical-line">
            <p class="account-label">My Account</p>
            <i class="bi bi-credit-card"></i>
            <p class="name-label"><?php echo $name; ?></p>
            <p class="number-label"><?php echo $contact_num; ?></p>
        </div> <!-- Vertical line -->
        <p class="income">₱<?php echo number_format($wallet, 2); ?></p>
    </div>
</div>
<div class="transaction-con">
<p class="bal">Recent Transaction</p>
<div class="trans-header">
<p class="date">Date</p>
<p class="type">Type | Description</p>
<p class="amount">Amount</p>
<p class="stats">Status</p>
</div>

<?php
// Fetch transaction data based on product owner and logged-in customer's name
$sql = "SELECT * FROM transaction WHERE product_owner = '$name'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        // Display transaction information
        echo '<div class="row-con">';
        echo '<p class="data-date">' . $row["order_date"] . '</p>';
        echo '<div class="type-order">';
        echo '<p class="order-id"><span class="orderIDlbl">Payment for Order </span>' . $row["order_id"] . '</p>';
        echo '<div class="namename">';
        echo '<p class="cust-name">' . $row["name"] . ' |</p>';
        echo '<p class="payment-order">' . $row["product_name"] . '</p>';
        echo '</div>';
        echo '</div>';
        echo '<p class="amount-paid">+ ₱' . number_format($row["total_amount"], 2) . '</p>';
        echo '<p class="transaction-status">' . $row["status"] . '</p>';
        echo '</div>';
    }
} else {
    // No transactions found
    echo '<div class="row-con">';
    echo '<p class="data-date">No transactions found</p>';
    echo '</div>';
}
?>
</div>
<!-- Footer -->
  <footer class="text-center text-white" style="background-color: #2d4158">
    <!-- Grid container -->
    <div class="container">
      <!-- Section: Links -->
      <section class="mt-5">
        <!-- Grid row-->
        <div class="row text-center d-flex justify-content-center pt-5">
          <!-- Grid column -->
          <div class="col-md-2">
            <h6 class="text-uppercase font-weight-bold">
              <a href="About.php" class="text-white" style="text-decoration: none">About us</a>
            </h6>
          </div>
          <!-- Grid column -->
          <!-- Grid column -->
          <div class="col-md-2">
            <h6 class="text-uppercase font-weight-bold">
              <a href="FAQ.php" class="text-white" style="text-decoration: none">FAQ's</a>
            </h6>
          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-2">
            <h6 class="text-uppercase font-weight-bold">
              <a href="Contact.php" class="text-white" style="text-decoration: none">Contact</a>
            </h6>
          </div>
          <!-- Grid column -->
        </div>
        <!-- Grid row-->
      </section>
      <!-- Section: Links -->

      <hr class="my-5" />

      <!-- Section: Text -->
      <section class="mb-5">
        <div class="row d-flex justify-content-center">
          <div class="col-lg-8">
            <p>
            Our Company, “UNBOXED” is on a mission to revolutionize self-promotion. Our platform offers users  a dynamic space to showcase their talents, projects, and business in a visually appealing and interactive manner. It’s all about breaking free from the norm and expressing uniqueness. Unboxed is designed to empower individuals who might feel overlooked or underestimated, providing them with a dedicated platform to share their passion and achievements. It’s about going outside the box and letting your creativity shine. Whether you’re an artist, entrepreneur, or innovator, Unboxed is here to help you stand out and make your mark. 
            </p>
          </div>
        </div>
      </section>
      <!-- Section: Text -->

      <!-- Section: Social -->
      <section class="text-center mb-5">
        <a href="Facebook.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-facebook"></i>
        </a>
        <a href="Twitter.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-twitter"></i>
        </a>
        <a href="Google.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-google"></i>
        </a>
        <a href="Instagram.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-instagram"></i>
        </a>
        <a href="LinkedIn.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-linkedin"></i>
        </a>
        <a href="Github.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-github"></i>
        </a>
      </section>
      <!-- Section: Social -->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div
         class="text-center p-3"
         style="background-color: rgba(0, 0, 0, 0.2)"
         >
      © 2020 Copyright:
      <a class="text-white" href="" style="text-decoration: none"
         >Unboxed</a
        >
    </div>
    <!-- Copyright -->
</footer>
  <!-- Footer -->
</body>
<script>
function redirectToProfile(name, image_dp) {
        window.location.href = "Profile.php?name=" + encodeURIComponent(name) + "&image_dp=" + encodeURIComponent(image_dp);
    }
</script>

</html>

<?php
$conn->close(); // Close the connection
?>
