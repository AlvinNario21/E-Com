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

// Retrieve the customer_id from the session
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    // Handle case when customer_id is not set
    echo "Customer ID not found.";
    exit; // Exit script if customer_id is not set
}

// Initialize $statusCounts as an empty array
$statusCounts = array();

// Count the number of items for each status category
$statusQueries = array(
    "All" => "SELECT COUNT(*) AS count FROM order_item WHERE customer_id = $customer_id",
    "Pending" => "SELECT COUNT(*) AS count FROM order_item WHERE customer_id = $customer_id AND status = 'Pending'",
    "To Ship" => "SELECT COUNT(*) AS count FROM order_item WHERE customer_id = $customer_id AND status = 'To Ship'",
    "To Receive" => "SELECT COUNT(*) AS count FROM order_item WHERE customer_id = $customer_id AND status = 'To Receive'",
    "Completed" => "SELECT COUNT(*) AS count FROM order_item WHERE customer_id = $customer_id AND status = 'Completed'",
    "Rated" => "SELECT COUNT(*) AS count FROM order_item WHERE customer_id = $customer_id AND status = 'Rated'",
);

foreach ($statusQueries as $status => $query) {
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $statusCounts[$status] = $result->fetch_assoc()['count'];
    } else {
        // Set default value if query fails
        $statusCounts[$status] = 0;
    }
}
// Check if the form is submitted for updating status
if (isset($_POST['order_item_id'])) {
    $orderItemId = $_POST['order_item_id'];
    updateOrderStatus($conn, $orderItemId);
}

// Function to send AJAX request to update order status, deduct stock, and add to sold quantity
function updateOrderStatus($conn, $orderItemId) {
    // Prepare statement to retrieve order details
    $orderStmt = $conn->prepare("SELECT product_id, quantity FROM order_item WHERE order_item_id = ?");
    $orderStmt->bind_param("i", $orderItemId);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();

    if ($orderResult->num_rows > 0) {
        $orderRow = $orderResult->fetch_assoc();
        $productId = $orderRow['product_id'];
        $quantity = $orderRow['quantity'];

        // Prepare statement to update product table
        $updateProductStmt = $conn->prepare("UPDATE product SET stocks = stocks - ?, sold = sold + ? WHERE product_id = ?");
        $updateProductStmt->bind_param("iii", $quantity, $quantity, $productId);
        if ($updateProductStmt->execute()) {
            // Prepare statement to update order status
            $updateStatusStmt = $conn->prepare("UPDATE order_item SET status = 'Completed' WHERE order_item_id = ?");
            $updateStatusStmt->bind_param("i", $orderItemId);
            if ($updateStatusStmt->execute()) {
                echo "Stock deducted and order status updated successfully.";
            } else {
                echo "Error updating order status: " . $updateStatusStmt->error;
            }
        } else {
            echo "Error updating product stock: " . $updateProductStmt->error;
        }
    } else {
        echo "Order not found.";
    }

    // Close statements
    $orderStmt->close();
    $updateProductStmt->close();
    $updateStatusStmt->close();
}
// Check if the form is submitted for updating status
if (isset($_POST['orderItemId'])) {
    // Retrieve the customer_id from the session
    if (isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];
        $customer_name = $_SESSION['name']; // Retrieve user's name from session
        $customer_img = $_SESSION['image_dp']; // Retrieve user's image_dp from session
    } else {
        // Handle case when customer_id is not set
        echo "Customer ID not found.";
        exit; // Exit script if customer_id is not set
    }

    // Retrieve product_id associated with order_item_id
    $orderItemId = $_POST['orderItemId'];
    $product_id_query = "SELECT product_id FROM order_item WHERE order_item_id = ?";
    $product_id_stmt = $conn->prepare($product_id_query);
    $product_id_stmt->bind_param("i", $orderItemId);
    $product_id_stmt->execute();
    $product_id_result = $product_id_stmt->get_result();

    if ($product_id_result->num_rows > 0) {
        $row = $product_id_result->fetch_assoc();
        $product_id = $row['product_id'];
    } else {
        // Handle case when product_id is not found
        echo "Product ID not found for order item ID: $orderItemId";
        exit; // Exit script if product_id is not found
    }

    // SQL query to insert data into the reviews table
    $insert_query = "INSERT INTO reviews (order_item_id, product_id, customer_id, customer_name, customer_img, rating, review_text, review_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    // Prepare the SQL statement
    $insert_stmt = $conn->prepare($insert_query);

    // Check if the statement was prepared successfully
    if ($insert_stmt === false) {
        die("Error in preparing insert statement: " . $conn->error);
    }

    // Bind parameters to the prepared statement
    $insert_stmt->bind_param("iiissss", $orderItemId, $product_id, $customer_id, $customer_name, $customer_img, $_POST['rating'], $_POST['reviewText']);

    // Execute the statement
    if ($insert_stmt->execute()) {
        // Update the status of the order_item to "Rated"
        $update_status_query = "UPDATE order_item SET status = 'Rated' WHERE order_item_id = ?";
        $update_status_stmt = $conn->prepare($update_status_query);
        $update_status_stmt->bind_param("i", $orderItemId);
        if ($update_status_stmt->execute()) {
            echo "Review submitted successfully. Order status updated to 'Rated'.";
        } else {
            echo "Error updating order status: " . $update_status_stmt->error;
        }
    } else {
        echo "Error submitting review: " . $insert_stmt->error;
    }

    // Close the statements
    $insert_stmt->close();
    $update_status_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unboxed Cart</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Include Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <!-- Include custom CSS -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="cart-container">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <div class="nav-logo">
                <a class="navbar-brand" href="Home.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
                <p class="navbar-brand">|</p>
                <p class="navbar-brand">My Purchase</p>
            </div>
            <div class="navbar-nav" id="navbarLinks">
                <a class="nav-link" href="#">ABOUT</a>
                <a class="nav-link" href="#">FAQ</a>
                <!-- Display user information if logged in -->
                <?php if ($displayUserInfo && !empty($name)): ?>
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
                    <!-- If user is not logged in, display Sign Up and Login links -->
                    <a class="nav-link" href="Create-Box.php">Sign Up</a>
                    <a class="nav-link">|</a>
                    <a class="nav-link" href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Search bar -->
    <div class="input-group-cart mb-3">
        <input type="text" class="form-control-cart" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2">
        <div class="input-group-append2">
            <i class="bi bi-search"></i>
        </div>
    </div>

    <!-- Navigation tabs for different order statuses -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="?status=All">All <span class="badge bg-primary"><?php echo $statusCounts['All']; ?></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?status=Pending">Pending <span class="badge bg-primary"><?php echo $statusCounts['Pending']; ?></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?status=To Ship">To Ship <span class="badge bg-primary"><?php echo $statusCounts['To Ship']; ?></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?status=To Receive">To Receive <span class="badge bg-primary"><?php echo $statusCounts['To Receive']; ?></span></a>
        </li>
        <li class="nav-item">
    <a class="nav-link" href="?status=Completed">Completed <span class="badge bg-primary"><?php echo $statusCounts['Completed'] + $statusCounts['Rated']; ?></span></a>
</li>
    </ul>

    <?php
   // Retrieve the status from the query parameter
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : 'All';

// Function to display order details based on selected status and customer_id
function displayOrderDetails($conn, $selectedStatus, $customer_id) {
    // Modify your SQL query to filter by status and customer_id if selectedStatus is not "All"
    if ($selectedStatus === 'Completed') {
        $sql = "SELECT * FROM order_item WHERE customer_id = $customer_id AND (status = 'Rated' OR status = 'Completed')";
    } else {
        $sql = ($selectedStatus === 'All') ? "SELECT * FROM order_item WHERE customer_id = $customer_id" : "SELECT * FROM order_item WHERE customer_id = $customer_id AND status = '$selectedStatus'";
    }
    $result = $conn->query($sql);

    // Display the order details
    if ($result->num_rows > 0) {
        // Store fetched rows in an array
        $orderItems = [];
        while ($row = $result->fetch_assoc()) {
            $orderItems[] = $row;
        }

        // Reverse the array to display latest items first
        $orderItems = array_reverse($orderItems);

        // Start a new div container for the order details
        echo "<div class='order-con' id='orderContainer'>";
        // Loop through each row of the result set
        foreach ($orderItems as $row) {
            // Display product details for each row
            echo "<div class='product-details-purchase'>";
            echo "<p class='order-status'>" . $row['status'] . "</p>";
            echo "<div class='order-name-img'>";
            echo "<img src='" . $row['product_img'] . "' class='product-img' alt=''>";
            echo "<p class='prod-name-order'>" . $row['product_name'] . "</p>";
            echo "<p class='prod-quantity-order'>" . $row['quantity'] . "x</p>";
            echo "</div>";
            echo "<div class='purchase-price'>";
            echo "<p class='prod-price-order'>₱" . $row['price'] . "</p>";
            // Calculate total price
            echo "</div>"; // Close product-details-checkout div
            echo "<div class='purchase-total'>";
            $total_payment = $row['price'] * $row['quantity'];
            echo "<p class='total-label-order'>Order Total: </p>";
            echo "<p class='total-pay'>₱" . $total_payment . ".00</p>";
            // Add button based on status
            if ($row['status'] === 'Completed') {
                // Display "Write Review" button
                echo "<button class='btn btn-primary write-review-button' data-order-item-id='" . $row['order_item_id'] . "'>Write Review</button>";
            } elseif ($row['status'] === 'Rated') {
                // Display "Order Received" button
                echo "<button class='btn btn-primary order-buy-button' data-order-item-id='" . $row['order_item_id'] . "'>Buy Again</button>";
            } elseif ($row['status'] === 'To Receive'){
                echo "<button class='btn btn-primary order-received-button' data-order-item-id='" . $row['order_item_id'] . "'>Order Received</button>";
            }else {
                // Display a disabled button for other statuses
                echo "<button class='btn btn-primary order-received-button' disabled>Order Received</button>";
            }

            echo "</div>"; // Close product-details-checkout div
            echo "</div>"; // Close product-details-purchase div
        }
        // Close the container div
        echo "</div>"; // Close order-con div
    } else {
        echo "No orders found for this customer.";
    }
}

// Call the function to display order details with the selected status
displayOrderDetails($conn, $selectedStatus, $customer_id);
?>

<!-- Modal -->
<div class="modal fade" id="writeReviewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Write Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Review Form -->
        <form id="reviewForm">
        <div class="mb-3">
    <label for="rating" class="form-label">Rating:</label>
    <div class="rating">
        <input type="hidden" id="rating" name="rating">
        <span class="star" data-rating="5">&#9733;</span>
        <span class="star" data-rating="4">&#9733;</span>
        <span class="star" data-rating="3">&#9733;</span>
        <span class="star" data-rating="2">&#9733;</span>
        <span class="star" data-rating="1">&#9733;</span>
    </div>
</div>

          <div class="mb-3">
            <label for="reviewText" class="form-label">Review:</label>
            <textarea class="form-control-review" id="reviewText" name="reviewText" rows="3" required></textarea>
          </div>
          <input type="hidden" id="orderItemId" name="orderItemId">
          <button type="submit" class="submit-review-btn" name="submitbtn">Submit Review</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- Custom JavaScript -->
<script>
// Add event listener to stars for rating selection
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('rating');

stars.forEach(function(star) {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-rating');
        ratingInput.value = rating;
        // Update star colors based on selected rating
        stars.forEach(function(s) {
            if (parseInt(s.getAttribute('data-rating')) <= parseInt(rating)) {
                s.style.color = '#fdd835'; // Change to your desired highlight color
            } else {
                s.style.color = '#ccc'; // Change to your desired default color
            }
        });
    });
});

// Add event listener to all "Write Review" buttons
const writeReviewButtons = document.querySelectorAll('.write-review-button');
writeReviewButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        const orderItemId = this.getAttribute('data-order-item-id');
        // Set the orderItemId in the hidden input field
        document.getElementById('orderItemId').value = orderItemId;
        // Show the modal
        const writeReviewModal = new bootstrap.Modal(document.getElementById('writeReviewModal'));
        writeReviewModal.show();
    });
});

// Submit review form via AJAX
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'Purchase.php'); // Replace 'Purchase.php' with your PHP endpoint
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Handle success response
            // For example, close modal and show a success message
            const writeReviewModal = bootstrap.Modal.getInstance(document.getElementById('writeReviewModal'));
            writeReviewModal.hide();
            alert('Review submitted successfully.');

            // Reload the page
            window.location.href = window.location.href;
        } else {
            // Handle error response
            console.error('Error submitting review:', xhr.statusText);
        }
    };
    xhr.send(formData);
});

document.addEventListener("DOMContentLoaded", function() {
    // Add event listener to all "Order Received" buttons
    const orderReceivedButtons = document.querySelectorAll('.order-received-button');
    orderReceivedButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const orderItemId = this.getAttribute('data-order-item-id');
            // Send AJAX request to update status
            updateOrderStatus(orderItemId);
        });
    });
});

// Function to send AJAX request to update order status
function updateOrderStatus(orderItemId) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'Purchase.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Reload page or update UI as needed
            location.reload(); // Reload page for simplicity
        } else {
            console.error('Error updating order status');
        }
    };
    xhr.send('order_item_id=' + orderItemId);
}
</script>
</body>
</html>

<?php
$conn->close(); // Close the connection
?>
