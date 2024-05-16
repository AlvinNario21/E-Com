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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the accepted button is clicked
    if (isset($_POST["accepted"])) {
        // Get the order item ID from the form
        $order_item_id = $_POST["order_item_id"];

        // Update the order_item status to "Accepted"
        $updateOrderItemQuery = "UPDATE order_item SET status = 'Accepted' WHERE order_item_id = ?";
        $stmt = $conn->prepare($updateOrderItemQuery);
        $stmt->bind_param("i", $order_item_id);
        $stmt->execute();
        $stmt->close();

        // Update the return_refund_requests status to "Accepted" (if needed)
        $updateRRRQuery = "UPDATE return_refund_requests SET status = 'Accepted' WHERE order_id = ?";
        $stmt = $conn->prepare($updateRRRQuery);
        $stmt->bind_param("i", $order_item_id);
        $stmt->execute();
        $stmt->close();

    } elseif (isset($_POST["received"])) {
        // Get the order item ID from the form
        $order_item_id = $_POST["order_item_id"];

        // Update the order_item status to "Returned"
        $updateOrderItemQuery = "UPDATE order_item SET status = 'Returned' WHERE order_item_id = ?";
        $stmt = $conn->prepare($updateOrderItemQuery);
        $stmt->bind_param("i", $order_item_id);
        $stmt->execute();
        $stmt->close();

        // Fetch total_payment and customer_name from order_item
        $fetchOrderItemQuery = "SELECT total_payment, customer_name FROM order_item WHERE order_item_id = ?";
        $stmt = $conn->prepare($fetchOrderItemQuery);
        $stmt->bind_param("i", $order_item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orderItem = $result->fetch_assoc();
        $stmt->close();

        $total_payment = $orderItem['total_payment'];
        $customer_name = $orderItem['customer_name'];

        // Update the wallet of the customer
        $updateWalletQuery = "UPDATE customer SET wallet = wallet + ? WHERE name = ?";
        $stmt = $conn->prepare($updateWalletQuery);
        $stmt->bind_param("ds", $total_payment, $customer_name);
        $stmt->execute();
        $stmt->close();
    }
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
            <a class="navbar-brand" href="Home.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
            <p class="navbar-brand">|</p>
            <p class="navbar-brand">Return/Refund</p>
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
        <input type="text" class="input-searc-return" name="searchTerm" placeholder="Search...">
        <button type="submit" name="search" class="btn-search"><i class="bi bi-search"></i></button>
    </form>
</div>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link" href="?tab=all">All</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?tab=request">Request</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?tab=returned">Returned</a>
    </li>
</ul>

<div class="parcel">

<?php
// Fetch order items with status "Request Return", "To Ship Return", or "To Receive Return" and where the logged-in customer's name matches the product owner
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    // Retrieve the search term
    $searchTerm = $_POST["searchTerm"];

    // Prepare the SQL statement to search for product_name in order_item with status "Request Return" and matching customer_name
    $orderItemsQuery = "SELECT oi.order_item_id, oi.product_img, oi.product_name, oi.quantity, oi.total_payment, oi.status, rrr.reason
                        FROM order_item oi 
                        INNER JOIN customer c ON oi.product_owner = c.name
                        LEFT JOIN return_refund_requests rrr ON oi.order_item_id = rrr.order_item_id
                        WHERE (oi.product_name LIKE ? AND (oi.status = 'Request Return' OR oi.status = 'To Ship Return' OR oi.status = 'To Receive Return' OR oi.status = 'Returned')) AND c.name = ?";
    $stmt = $conn->prepare($orderItemsQuery);

    // Bind parameters
    $searchTerm = "%" . $searchTerm . "%"; // Add wildcard '%' before and after the search term for partial matching
    $stmt->bind_param("ss", $searchTerm, $_SESSION["name"]);

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
        case 'request':
            $status_condition = "oi.status = 'Request Return'";
            break;
        case 'returned':
            $status_condition = "oi.status = 'Returned'";
            break;
        default:
            $status_condition = "(oi.status = 'Request Return' OR oi.status = 'To Ship Return' OR oi.status = 'To Receive Return' OR oi.status = 'Returned')";
            break;
    }

    $orderItemsQuery = "SELECT oi.order_item_id, oi.product_img, oi.product_name, oi.quantity, oi.total_payment, oi.status, rrr.reason
                        FROM order_item oi 
                        INNER JOIN customer c ON oi.product_owner = c.name
                        LEFT JOIN return_refund_requests rrr ON oi.order_id = rrr.order_id
                        WHERE " . $status_condition . " AND c.name = ?";
    $stmt = $conn->prepare($orderItemsQuery);
    $stmt->bind_param("s", $_SESSION["name"]);
    $stmt->execute();
    $orderItemsResult = $stmt->get_result();
    $stmt->close();
}

// Inside the while loop where you display order items
while ($item = $orderItemsResult->fetch_assoc()): ?>
    <div class="parcel-card">
        <form method="POST" action="request&return.php">
            <input type="hidden" name="order_item_id" value="<?php echo htmlspecialchars($item['order_item_id']); ?>">
            <?php if ($item['status'] == 'To Receive Return'): ?>
                <button type="submit" name="received" class="delivered-btn">Received</button>
            <?php elseif ($item['status'] == 'Request Return'): ?>
                <button type="submit" name="accepted" class="delivered-btn">Accept Proposal</button>
                <button type="submit" name="rejected" class="discuss-btn">Reject</button>
            <?php endif; ?>
        </form>
        <p class="prod-status"><?php echo htmlspecialchars($item['status']); ?></p>
        <?php if ($item['status'] !== 'Returned'): ?>
            <p class="return-reason">Reason: <?php echo htmlspecialchars($item['reason']); ?></p>
        <?php endif; ?>
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