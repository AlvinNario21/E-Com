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

// Fetch name and image_dp from the customer table
$sql = "SELECT customer_id, name, image_dp FROM customer";
$result = $conn->query($sql);

// Check if the user is logged in
if (isset($_SESSION["email"])) {
    // User is logged in, fetch the user's name and image_dp from the session
    $name = $_SESSION["name"]; // Assuming 'name' is the column name for user's name
    $image_dp = $_SESSION["image_dp"]; // Assuming 'image_dp' is the column name for user's image
   
} else {
    // User is not logged in, set default values
    $name = "";
    $image_dp = "";
}
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
<div class="cart-container">
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="nav-logo">
            <a class="navbar-brand" href="Home.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
            <p class="navbar-brand">|</p>
            <p class="navbar-brand">Checkout</p>
        </div>
        <div class="navbar-nav" id="navbarLinks">
            <a class="nav-link" href="#">ABOUT</a>
            <a class="nav-link" href="#">FAQ</a>
            <?php if ($displayUserInfo && !empty($name)): ?>
                <!-- Display user's name and image_dp -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                    </a>
                    <ul class="dropdown-menu" id="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="Profile.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">Profile</a>
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

<div class="menu-checkout">
<div class="form-check">
  <label class="form-check-label-out" for="flexCheckDefault">
    Products Ordered
  </label>
</div>
<p>Unit Price</p>
  <p>Amount</p>
  <p>Item Subtotal</p>
</div>
<?php
if (isset($_SESSION["email"]) && isset($_SESSION["customer_id"])) {
    // User is logged in, fetch the user's name and image_dp from the session
    $name = $_SESSION["name"]; // Assuming 'name' is the column name for user's name
    $image_dp = $_SESSION["image_dp"]; // Assuming 'image_dp' is the column name for user's image
    $customer_id = $_SESSION["customer_id"]; // Get the customer_id from the session

    // Modify the SQL query to fetch only items belonging to the logged-in customer
    $sql = "SELECT ci.*, p.product_img, p.name as product_name, p.price as unit_price
            FROM cart_items ci
            INNER JOIN product p ON ci.product_id = p.product_id
            WHERE ci.customer_id = '$customer_id'";
    $result = $conn->query($sql);

    if ($result) {
        // Initialize an array to keep track of products already displayed
        $displayed_products = array();

        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                // Display each cart item
                echo '<div class="product-details-checkout">';
                echo '<div class="form-check">';
                echo '</div>';
                echo '<img src="' . $row['product_img'] . '" class="product-img" alt="">';
                echo '<p class="prod-name-check">' . $row['product_name'] . '</p>';
                echo '<p class="prod-price-check">₱' . $row['unit_price'] . '</p>';
                echo '<p class="prod-quantity-check">' . $row['quantity'] . '</p>'; // Output the quantity from the current row
                // Calculate total price
                $total_price = $row['unit_price'] * $row['quantity']; // Use quantity from the current row
                echo '<p class="total-price">₱' . $total_price . '</p>';
                echo '</div>';
            }
        } else {
            echo "No items in the cart.";
        }
    } else {
        echo "Error fetching cart items: " . $conn->error;
    }
} else {
    header("Location: login.php");
    exit; // Ensure that subsequent code is not executed after the redirection
}
?>
<div class="total">
<p>₱20000</p>
<p>Order Total (1 Items):</p>
</div>
<div class="payment">
        <p class=payment-label>Payment Method</p>
        <select class="payment-address" aria-label="Default select example">
    <option selected>Choose Payment Method</option>
    <!-- Assume the options will be dynamically populated by PHP -->
    <option value="gcash">Gcash</option>
    <option value="cod">Cash On Delivery</option>
</select>
</div>
<div class="payment-details">
    <div class="merch-total">
    <p>₱20000</p>
    <p>Merchandise Subtotal:</p>
    </div>
    <div class="ship-total">
    <p>₱50</p>
    <p>Shipping Total: </p>
</div>
<div class="total-payment">
    <p>Total Payment: </p>
    <p>₱20000</p>
</div>

</div>
<div class="placeorder">
<button type="button" class="btn btn-outline-light">Place Order</button>
</div>
</div>
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