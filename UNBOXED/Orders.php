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

// Start session to manage user login state

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

// Fetch data from order_item where product_owner matches owner from product
if (isset($_SESSION["name"])) {
    $customer_name = $_SESSION["name"];
    $orderItemQuery = "SELECT * FROM order_item oi JOIN product p ON oi.product_id = p.product_id WHERE p.owner = '$name'";
    $orderItemResult = $conn->query($orderItemQuery);
}

// Check if the user is logged in
if (isset($_SESSION["email"])) {
    // User is logged in, fetch the user's name, contact number, and address from the database
    $email = $_SESSION["email"]; // Assuming 'email' is the column name for user's email
    $sql = "SELECT name, contact_num, address FROM customer WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Fetch user's information
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $contact_num = $row["contact_num"];
        $address = $row["address"];

        // Store user information in session
        $_SESSION['name'] = $name;
        $_SESSION['contact_num'] = $contact_num;
        $_SESSION['address'] = $address;
    } else {
        // Handle case when user's information is not found
        $name = "Unknown";
        $contact_num = "Unknown";
        $address = "Unknown";
    }
} else {
    // Handle case when user is not logged in
    $name = "";
    $contact_num = "";
    $address = "";
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare the SQL statement for inserting into the "shippings" table
    $stmt = $conn->prepare("INSERT INTO shippings (recipient_name, product_id, pickup_address, shipping_company, shipping_date, pick_up_time, shipping_cost, shipping_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Error in preparing statement: " . $conn->error);
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("sissssss", $recipient_name, $product_id, $shipping_address, $shipping_company, $shipping_date, $pick_up_time, $shipping_cost, $shipping_status);

    // Initialize variables
    $recipient_name = isset($_POST['recipient-name']) ? $_POST['recipient-name'] : "";
    $product_id = isset($_POST['product-id']) ? $_POST['product-id'] : "";
    $shipping_address = isset($_SESSION['address']) ? $_SESSION['address'] : "";
    $shipping_company = isset($_POST['shipping-company']) ? $_POST['shipping-company'] : "";
    $shipping_date = isset($_POST['shipment-date']) ? $_POST['shipment-date'] : "";
    $pick_up_time = isset($_POST['shipment-time']) ? date('H:i:s', strtotime($_POST['shipment-time'])) : ""; // Convert time format to HH:MM:SS
    $shipping_cost = 55; // Assuming shipping cost is fixed
    $shipping_status = "To Ship";

    // Execute the SQL statement for insertion
    if ($stmt->execute()) {
        echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";

        // Close the statement
        $stmt->close();

        // Prepare the SQL statement for updating the "status" column in the "order_item" table
        $update_stmt = $conn->prepare("UPDATE order_item SET status = 'To Ship' WHERE order_item_id = ?");
        
        // Check if the statement was prepared successfully
        if ($update_stmt === false) {
            die("Error in preparing update statement: " . $conn->error);
        }

        // Bind parameters to the prepared statement
        $update_stmt->bind_param("i", $order_item_id);

        $order_item_id = isset($_POST['order-item-id']) ? $_POST['order-item-id'] : "";

        // Execute the SQL statement for updating status
        if ($update_stmt->execute()) {
            echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
        } else {
            // Output any errors
            echo "Error updating status: " . $update_stmt->error;
        }

        // Close the update statement
        $update_stmt->close();
    } else {
        // Output any errors
        echo "Error inserting record: " . $stmt->error;
    }
}

// Check if the form is submitted for updating status
if (isset($_POST['picked_up'])) {
    // Prepare the SQL statement for updating the "status" column in the "order_item" table
    $update_stmt = $conn->prepare("UPDATE order_item SET status = 'To Receive' WHERE order_item_id = ?");
    
    // Check if the statement was prepared successfully
    if ($update_stmt === false) {
        die("Error in preparing update statement: " . $conn->error);
    }

    // Bind parameters to the prepared statement
    $update_stmt->bind_param("i", $order_item_id);

    // Iterate through each selected item ID and update its status
    foreach ($_POST['checked_items'] as $order_item_id) {
        // Execute the SQL statement for updating status
        if ($update_stmt->execute()) {
                // Redirect to the Order.php page
                echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
                exit;

        } else {
            // Output any errors
            echo "Error updating status for order item with ID: $order_item_id - " . $update_stmt->error;
        }
    }

    // Close the update statement
    $update_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders</title>
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
            <p class="navbar-brand">Orders</p>
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

<div class="input-group-cart mb-3">
    <input type="text" class="form-control-cart" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2">
    <div class="input-group-append2">
        <i class="bi bi-search"></i>
    </div>
</div>

<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link" href="#">All</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">To Ship</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">To Receive</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Completed</a>
  </li>
</ul>

<?php
// Check if the query was successful
if ($orderItemResult) {
    // Display order items
    if ($orderItemResult->num_rows > 0) {
        // Start form
        echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post' id='shipment-form'>";
        echo "<button type='button' class='btn btn-primary order-ship-button' id='arrange-shipment-btn'>Arrange Shipment</button>";
        echo "<button type='submit' class='btn btn-success' id='pick-up-btn' name='picked_up'>Picked Up</button>";
        // Select all checkbox
        echo "<label><input type='checkbox' id='select-all'> Select All</label><br>";

        // Container for order items
        echo "<div id='order-items-container'>";
        // Fetch and display order items in reverse order
        $orderItems = $orderItemResult->fetch_all(MYSQLI_ASSOC);
        $orderItems = array_reverse($orderItems);
        foreach ($orderItems as $row) {
           // Display each order item
           echo "<div class='product-details-order'>";
           echo "<p class='order-status'>" . $row['status'] . "</p>";
           echo "<div class='order-check'>";
           echo "<input type='checkbox' class='item-checkbox' name='checked_items[]' value='" . $row['order_item_id'] . "' data-product-id='" . $row['product_id'] . "' data-customer-name='" . $row['customer_name'] . "'>"; // Checkbox for each item
           // Checkbox for each item
           echo "<div class='order-name-img'>";
           echo "<img src='" . $row['product_img'] . "' class='product-img' alt=''>";
           echo "<p class='prod-name-order'>" . $row['product_name'] . "</p>";
           echo "<p class='prod-quantity-order'>" . $row['quantity'] . "x</p>";
           echo "</div>";
           echo "<div class='order-price'>";
           echo "<p class='prod-price-order'>₱" . $row['price'] . "</p>";
           // Calculate total price
           echo "</div>"; // Close product-details-checkout div
           echo "<div class='order-total'>";
           $total_payment = $row['price'] * $row['quantity'];
           echo "<p class='total-label'>Order Total: </p>";
           echo "<p class='total-payment'>₱" . $total_payment . ".00</p>";
           echo "</div>"; 
           echo "</div>"; 
           echo "</div>"; 
           // Add more details as needed
        }
        // Close container
        echo "</div>"; // Close order-items-container
        // End form
        echo "</form>";
    } else {
        echo "No orders found.";
    }
} else {
    echo "Error fetching order items: " . $conn->error;
}

?>

<div class="modal fade" id="shipmentModal" tabindex="-1" aria-labelledby="shipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <form id="shipmentForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="shipmentModalLabel">Arrange Shipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" id="order-item-id" name="order-item-id">
                    <!-- Display recipient name and product ID -->
                    <div class="mb-3">
    <label for="recipient-name" class="form-label">Recipient Name:</label>
    <input type="text" class="form-control-ship" id="recipient-name" name="recipient-name" readonly>
</div>

                    <div class="mb-3">
                        <label for="product-id" class="form-label">Product ID:</label>
                        <input type="text" class="form-control" id="product-id" name="product-id" readonly>
                    </div>

                    <!-- Shipment form fields (number of items selected, date picker, time picker, and address of the logged-in user) -->
                    <div class="mb-3">
                        <label for="selected-items" class="form-label">Number of Items Selected:</label>
                        <input type="text" class="form-control-ship" id="selected-items" value="0" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="shipment-date" class="form-label">Shipment Date:</label>
                        <input type="date" class="form-control-ship" id="shipment-date" name="shipment-date" required>
                    </div>

                    <!-- Time picker -->
                    <div class="mb-3">
                        <label for="shipment-time" class="form-label">Shipment Time:</label>
                        <input type="time" class="form-control-ship" id="shipment-time" name="shipment-time" required>
                    </div>

                    <!-- Address of the logged-in user -->
                    <div class="mb-3">
                        <label for="address" class="form-label">Pick-up Address:</label>
                        <textarea class="form-control-ship" id="user-address" name="user-address" rows="3" readonly><?php echo $address; ?></textarea>
                    </div>

                    <!-- Shipping company selection -->
                    <div class="mb-3">
                        <label class="form-label">Shipping Company:</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="shipping-company" id="jt-express" value="J&T Express">
                            <label class="form-check-label" for="jt-express">J&T Express</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="shipping-company" id="ninja-van" value="Ninja Van">
                            <label class="form-check-label" for="ninja-van">Ninja Van</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="shipping-company" id="flash-express" value="Flash Express">
                            <label class="form-check-label" for="flash-express">Flash Express</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- Add an id attribute to the "Submit Shipment" button -->
                    <button type="submit" class="btn btn-primary" id="submitShipmentBtn">Submit Shipment</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
<!-- Include this JavaScript code in your HTML file -->
<script>  
 // JavaScript function to handle button click and show modal
 document.getElementById('arrange-shipment-btn').addEventListener('click', function() {
        // Get all checked checkboxes
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');

        // Check if at least one item is checked
        if (checkedItems.length > 0) {
            // Get the modal
            var modal = new bootstrap.Modal(document.getElementById('shipmentModal'));
            // Show the modal
            modal.show();

            // Get the order_item_id, product id, and customer name for the first checked item
            const firstCheckedItem = checkedItems[0];
            const orderItemId = firstCheckedItem.value;
            const productId = firstCheckedItem.dataset.productId;
            const customerName = firstCheckedItem.dataset.customerName;

            // Update the modal fields with the order_item_id, product id, and customer name
            document.getElementById('order-item-id').value = orderItemId;
            document.getElementById('product-id').value = productId;
            document.getElementById('recipient-name').value = customerName;
        } else {
            // If no item is checked, show an alert
            alert('Please select at least one item for shipment.');
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Get all checkboxes
        const checkboxes = document.querySelectorAll('.item-checkbox');
        // Get the input field for displaying the number of selected items
        const selectedItemsField = document.getElementById('selected-items');

        // Add event listener to each checkbox
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Count the number of checked checkboxes
                const checkedItems = document.querySelectorAll('.item-checkbox:checked');
                // Update the value of the input field to display the number of selected items
                selectedItemsField.value = checkedItems.length;
            });
        });
    });

    document.getElementById('picked-up-btn').addEventListener('click', function() {
    // Submit the form when the "Picked Up" button is clicked
    document.getElementById('shipment-form').submit();
});

    function redirectToProfile(name, image_dp) {
        window.location.href = "Profile.php?name=" + encodeURIComponent(name) + "&image_dp=" + encodeURIComponent(image_dp);
    }
</script>

</html>

<?php
$conn->close(); // Close the connection
?>
