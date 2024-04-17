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
    // User is logged in, fetch the user's customer_id from the session
    $customer_id = isset($_SESSION["customer_id"]) ? $_SESSION["customer_id"] : '';

    // Fetch count of items in the cart for the logged-in customer
    $cart_item_count = 0;
    if (!empty($customer_id)) {
        $count_query = "SELECT COUNT(*) AS total_items FROM cart_items WHERE customer_id = '$customer_id'";
        $count_result = $conn->query($count_query);
        if ($count_result && $count_result->num_rows > 0) {
            $count_row = $count_result->fetch_assoc();
            $cart_item_count = $count_row['total_items'];
        }
    }
} else {
    // User is not logged in, set default count to 0
    $cart_item_count = 0;
}

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
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="nav-logo">
            <a class="navbar-brand" href="Home.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
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
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
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

<div class="input-group mb-3">
    <input type="text" class="form-control" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2">
    <div class="input-group-append">
        <i class="bi bi-search"></i>
    </div>
</div>
<div class="count-cart-items">
    <?php if ($cart_item_count > 0): ?>
        <!-- Display cart item count if greater than 0 -->
        <p><?php echo $cart_item_count; ?></p>
    <?php endif; ?>
</div>
<div class="cart">
    <a href="Cart.php"><i class="bi bi-cart4"></i></a>
</div>

<div class="card-container" onclick="redirectToProfile('<?php echo urlencode($name); ?>', '<?php echo urlencode($image_dp); ?>')">
    <?php
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Check if 'customer_id' key exists in the row
            if (isset($row['customer_id'])) {
                // Check if the user is in guest mode or if the name matches the logged-in user's name
                if (!$displayUserInfo || $row["name"] !== $name) {
                    // Display the card if the user is in guest mode or if the name does not match the logged-in user's name
                    echo '<div class="card" style="width: 18rem;">';
                    echo '<a href="Profile.php?customer_id=' . $row["customer_id"] . '">'; // Link to Profile.php with the customer_id as a parameter
                    echo '<img src="' . $row["image_dp"] . '" class="card-img-top" alt="' . $row["name"] . '">';
                    echo '</a>';
                    echo '<div class="card-body">';
                    echo '<p class="card-text">' . $row["name"] . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "Customer ID not found for this row.";
            }
        }
    } else {
        echo "0 results";
    }
    ?>
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
