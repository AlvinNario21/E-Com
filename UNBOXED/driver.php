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
}

// Handle "Delivered" button click
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delivered"])) {
    $orderItemId = $_POST["order_item_id"];
    $updateStatusQuery = "UPDATE order_item SET status = 'Delivered' WHERE order_item_id = ?";
    $stmt = $conn->prepare($updateStatusQuery);
    $stmt->bind_param("i", $orderItemId);
    $stmt->execute();
    $stmt->close();
}

// Assuming a database connection is already established
// Fetch the driver's data from the database
if (isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
    $query = "SELECT name, image_dp FROM driver WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['name'] = $row['name'];
        $_SESSION['image_dp'] = $row['image_dp'];
    }
    $stmt->close();
} else {
    $_SESSION['name'] = "";
    $_SESSION['image_dp'] = "";
}

// Check if the driver is logged in
if (isset($_SESSION["email"])) {
    // Driver is logged in, fetch the driver's name and image_dp from the session
    $name = $_SESSION["name"];
    $image_dp = $_SESSION["image_dp"];
} else {
    // Driver is not logged in, set default values
    $name = "";
    $image_dp = "";
}

// Check if the session variable is set and true (meaning "Open the Boxes" button is clicked)
$displayDriverInfo = !(isset($_SESSION['open_boxes']) && $_SESSION['open_boxes']);

// Fetch all order items for display
$orderItemsQuery = "SELECT oi.order_item_id, oi.product_img, oi.product_name, oi.quantity, oi.total_payment, oi.status 
                    FROM order_item oi 
                    WHERE oi.status = 'To Receive'";
$orderItemsResult = $conn->query($orderItemsQuery);
$orderItems = [];
while ($row = $orderItemsResult->fetch_assoc()) {
    $orderItems[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNBOXED</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <div class="nav-logo">
            <a class="navbar-brand" href="driver.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
        </div>
        <div class="navbar-nav" id="navbarLinks">
            <a class="nav-link" href="About.php">ABOUT</a>
            <a class="nav-link" href="FAQ.php">FAQ</a>
            <?php if ($displayDriverInfo && !empty($name)): ?>
                <!-- Display driver's name and image_dp -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="Profile.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Profile</a>
                        <a class="dropdown-item" href="Purchase.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Purchase</a>
                        <a class="dropdown-item" href="Orders.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">Orders</a>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?logout=1">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <!-- If driver is not logged in or "Open the Boxes" button is clicked, display Sign Up and Login links -->
                <a class="nav-link" href="Create-Box.php">Sign Up</a>
                <a class="nav-link">|</a>
                <a class="nav-link" href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="search-name">
    <form method="POST" action="driver.php">
        <input type="text" class="input-search" name="searchTerm" placeholder="Search...">
        <button type="submit" name="search" class="btn-search"><i class="bi bi-search"></i></button>
    </form>
</div>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" href="?tab=all">All</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?tab=to_receive">To Receive</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?tab=delivered">Delivered</a>
    </li>
</ul>

<div class="parcel">

    <?php
    // Fetch order items with status "To Receive" for display or filtered order items based on search
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
        // Retrieve the search term
        $searchTerm = $_POST["searchTerm"];

        // Prepare the SQL statement to search for product_name in order_item with status "To Receive"
        $orderItemsQuery = "SELECT oi.order_item_id, oi.product_img, oi.product_name, oi.quantity, oi.total_payment, oi.status 
                            FROM order_item oi 
                            WHERE oi.product_name LIKE ? AND oi.status = 'To Receive'";
        $stmt = $conn->prepare($orderItemsQuery);

        // Bind parameters
        $searchTerm = "%" . $searchTerm . "%"; // Add wildcard '%' before and after the search term for partial matching
        $stmt->bind_param("s", $searchTerm);

        // Execute the query
        $stmt->execute();
        $orderItemsResult = $stmt->get_result();

        // Close statement
        $stmt->close();
    } else {
        // Check which tab is clicked
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        } else {
            $tab = 'all'; // Default tab
        }
    
        // Fetch order items based on the tab clicked
        switch ($tab) {
            case 'to_receive':
                $status_condition = "oi.status = 'To Receive'";
                break;
            case 'delivered':
                $status_condition = "oi.status = 'Delivered'";
                break;
            default:
                $status_condition = "(oi.status = 'To Receive' OR oi.status = 'Delivered')";
                break;
        }
    
        $orderItemsQuery = "SELECT oi.order_item_id, oi.product_img, oi.product_name, oi.quantity, oi.total_payment, oi.status 
                            FROM order_item oi 
                            WHERE " . $status_condition;
        $orderItemsResult = $conn->query($orderItemsQuery);
    }

    // Display order items
    while ($item = $orderItemsResult->fetch_assoc()): ?>
        <div class="parcel-card">
            <form method="POST" action="driver.php">
                <input type="hidden" name="order_item_id" value="<?php echo htmlspecialchars($item['order_item_id']); ?>">
                <button type="submit" name="delivered" class="delivered-btn">Delivered</button>
            </form>
            <p class="prod-status"><?php echo htmlspecialchars($item['status']); ?></p>
            <div class="order-name-img">
                <img src="<?php echo htmlspecialchars($item['product_img']); ?>" class="product-img" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                <p class="prod-name-order"><?php echo htmlspecialchars($item['product_name']); ?></p>
                <p class="prod-quantity-order"><?php echo htmlspecialchars($item['quantity']); ?>x</p>
            </div>
            <div class="order-price">
                <p class="prod-price-order">Price: ₱<?php echo htmlspecialchars(number_format($item['total_payment'], 2)); ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>