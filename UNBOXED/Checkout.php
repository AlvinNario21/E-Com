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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    // Retrieve user information from session
    // (Assuming these session variables are set elsewhere in your code)
    $customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
    $customer_name = isset($_SESSION['name']) ? $_SESSION['name'] : "";
    $address = isset($_SESSION['address']) ? $_SESSION['address'] : "";
    $contact = isset($_SESSION['contact_num']) ? $_SESSION['contact_num'] : "";

    // Generate order_id
    $order_id = uniqid(); // Generates a unique ID

    // Retrieve other form data
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : "";
    $product_img = isset($_POST['product_img']) ? $_POST['product_img'] : "";
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : "";
    
    // Fetch product owner from the database
    $product_owner = ""; // Initialize product_owner variable
    $fetch_owner_sql = "SELECT owner FROM product WHERE product_id = ?";
    $fetch_owner_stmt = $conn->prepare($fetch_owner_sql);
    $fetch_owner_stmt->bind_param("i", $product_id);
    $fetch_owner_stmt->execute();
    $fetch_owner_result = $fetch_owner_stmt->get_result();
    if ($fetch_owner_result->num_rows > 0) {
        $row = $fetch_owner_result->fetch_assoc();
        $product_owner = $row['owner'];
    }

    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : "";
    $price = isset($_POST['price']) ? $_POST['price'] : "";
    $total_payment = isset($_POST['total_payment']) ? $_POST['total_payment'] : "";
    $payment = isset($_POST['payment']) ? $_POST['payment'] : "";
    $order_date = isset($_POST['order_date']) ? $_POST['order_date'] : date("Y-m-d"); // Current date

    if ($payment == 'Gcash') {
        // Redirect to gcash_payment.php with necessary data as GET parameters
        $redirect_url = "gcash_payment.php?";
        $redirect_url .= "order_id=" . urlencode($order_id);
        $redirect_url .= "&customer_id=" . urlencode($customer_id);
        $redirect_url .= "&customer_name=" . urlencode($customer_name);
        $redirect_url .= "&address=" . urlencode($address);
        $redirect_url .= "&contact=" . urlencode($contact);
        $redirect_url .= "&product_id=" . urlencode($product_id);
        $redirect_url .= "&product_img=" . urlencode($product_img);
        $redirect_url .= "&product_name=" . urlencode($product_name);
        $redirect_url .= "&product_owner=" . urlencode($product_owner);
        $redirect_url .= "&quantity=" . urlencode($quantity);
        $redirect_url .= "&price=" . urlencode($price);
        $redirect_url .= "&total_payment=" . urlencode($total_payment);
        $redirect_url .= "&payment=" . urlencode($payment);
        $redirect_url .= "&order_date=" . urlencode($order_date);
        $redirect_url .= "&status=" . urlencode($status);
        header("Location: $redirect_url");
        exit;
    }

    $totalPayment = $merchandiseSubtotal + $shippingFee;
                 
    // Insert data into order_item table for other payment methods
    $sql = "INSERT INTO order_item (order_id, customer_id, customer_name, address, contact, product_id, product_img, product_name, product_owner, quantity, price, total_payment, payment, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

    // Prepare and execute statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("sisssissssdsss", $order_id, $customer_id, $customer_name, $address, $contact, $product_id, $product_img, $product_name, $product_owner, $quantity, $price, $total_payment, $payment, $order_date);

        // Execute statement
        if ($stmt->execute()) {
            // Order placed successfully, delete data from cart_items table
            $delete_sql = "DELETE FROM cart_items WHERE product_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            if ($delete_stmt) {
                // Bind parameter
                $delete_stmt->bind_param("i", $product_id);
                
                // Execute deletion
                if ($delete_stmt->execute()) {
                    // Redirect to Orders.php
                    header("Location: Purchase.php");
                    exit;
                } else {
                    echo "Error deleting from cart_items table: " . $delete_stmt->error;
                }
            } else {
                echo "Error preparing delete statement: " . $conn->error;
            }
        } else {
            // Print any SQL errors
            echo "Error executing insert statement: " . $stmt->error;
        }
    } else {
        // Print any SQL errors
        echo "Error preparing insert statement: " . $conn->error;
    }
}

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

    $productDetails = array();
    if (isset($_POST['checked_items']) && !empty($_POST['checked_items'])) {
        $selectedItemIDs = implode(",", array_map('intval', $_POST['checked_items']));
        $sql = "SELECT ci.*, p.product_img, p.name as product_name, p.price as unit_price
                FROM cart_items ci
                INNER JOIN product p ON ci.product_id = p.product_id
                WHERE ci.cart_item_id IN ($selectedItemIDs)";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $productDetails[] = $row;
            }
        }
    }
    if (isset($_POST['payment'])) {
        // Assign the selected payment method to the $payment variable
        $payment = $_POST['payment'];
    } else {
        // Default value if payment method is not selected
        $payment = "Not selected";
    }
    // Assign the current date and time to the $order_date variable
    $order_date = date("Y-m-d"); // Format: YYYY-MM-DD

    // Calculate merchandise subtotal
$merchandiseSubtotal = 0;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculate total price for each item and add it to the subtotal
        $total_price = $row['unit_price'] * $row['quantity'];
        $merchandiseSubtotal += $total_price;
    }
}

// Set a default shipping fee
$shippingFee = 55; // You can adjust this value as needed

// Calculate total payment (subtotal + shipping fee)
$totalPayment = $merchandiseSubtotal + $shippingFee;

// Check if the request is POST and the form from the modal is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['contact_num']) && isset($_POST['address'])) {
    // Retrieve form data from the POST request
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_num = $_POST['contact_num'];
    $address = $_POST['address'];
    $status = "Completed";

    // Prepare and execute the SQL statement to insert data into the invoice table
    $insert_sql = "INSERT INTO invoice (name, email, contact, address, status) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    if ($insert_stmt) {
        // Bind parameters
        $insert_stmt->bind_param("sssss", $name, $email, $contact_num, $address, $status);

        // Execute the statement
        if ($insert_stmt->execute()) {
            // Insertion successful
            echo "success";
        } else {
            // Insertion failed
            echo "Error requesting invoice: " . $insert_stmt->error;
        }
    } else {
        // Error preparing the statement
        echo "Error preparing insert statement: " . $conn->error;
    }
}
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Checkout</title>
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
            <a class="nav-link" href="About.php">ABOUT</a>
                <a class="nav-link" href="FAQ.php">FAQ</a>
                <?php if ($displayUserInfo && !empty($name)): ?>
                    <!-- Display user's name and image_dp -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                        </a>
                        <ul class="dropdown-menu" id="dropdown-menu" aria-labelledby="navbarDropdown">
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

    <div class="address-con">
        <div class="addresss">
            <i class="bi bi-geo-alt-fill"></i>
            <p>Delivery Address</p>
        </div>
        <div class="address-info">
            <p><?php echo $name; ?></p>
            <p><?php echo $contact_num; ?></p>
            <p><?php echo $address; ?></p>
        </div>
    </div>

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


        <!-- View Invoice Modal -->
<div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="viewInvoiceModalLabel">View Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Display invoice details -->
                <p>Name: <?php echo $name; ?></p>
                <p>Contact: <?php echo $contact_num; ?></p>
                <p>Email: <?php echo $_SESSION['email']; ?></p>
                <p>Address: <?php echo $address; ?></p>
                <!-- Add other invoice details here -->
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="requestInvoiceModal" tabindex="-1" aria-labelledby="requestInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="requestInvoiceModalLabel">Request E-Invoice</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="requestInvoiceForm" action="Checkout.php" method="POST">
          <div class="mb-3">
            <label for="name" class="form-label-contact">Name</label>
            <input type="text" class="form-control-name" id="name" name="name" readonly>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label-contact">Email address</label>
            <input type="email" class="form-control-name" id="email" name="email" readonly>
          </div>
          <div class="mb-3">
            <label for="contact_num" class="form-label-contact">Contact Number</label>
            <input type="text" class="form-control-name" id="contact_num" name="contact_num" readonly>
          </div>
          <div class="mb-3">
            <label for="address" class="form-label-contact">Address</label>
            <textarea class="form-control-name" id="address" rows="3" name="address" readonly></textarea>
          </div>
          <div class="modal-footer">
                    <button type="button" class="btn-close-add" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn-update-add" data-bs-dismiss="modal" id="submitInvoiceButton">Submit</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>

    <form id="checkoutForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <?php
    // Displaying selected items moved inside PHP block
    // Check if the selected item IDs are sent from the Cart page
    if (isset($_POST['checked_items']) && !empty($_POST['checked_items'])) {
        // Sanitize the input to prevent SQL injection
        $selectedItemIDs = implode(",", array_map('intval', $_POST['checked_items']));

        // Modify the SQL query to fetch only the selected items
        $sql = "SELECT ci.*, p.product_img, p.name as product_name, p.price as unit_price
                FROM cart_items ci
                INNER JOIN product p ON ci.product_id = p.product_id
                WHERE ci.cart_item_id IN ($selectedItemIDs)";
        $result = $conn->query($sql);

        $merchandiseSubtotal = 0;
        $shippingFee = 55; // Fixed shipping fee

        if ($result && $result->num_rows > 0) {
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
                $total_price = $row['unit_price'] * $row['quantity'];
                echo '<p class="total-price-check">₱' . $total_price . '</p>';
                $merchandiseSubtotal += $total_price;

                // Add hidden input fields to store product details
                echo '<input type="hidden" name="product_id" value="' . $row['product_id'] . '">';
                echo '<input type="hidden" name="product_img" value="' . $row['product_img'] . '">';
                echo '<input type="hidden" name="product_name" value="' . $row['product_name'] . '">';
                echo '<input type="hidden" name="quantity" value="' . $row['quantity'] . '">';
                echo '<input type="hidden" name="price" value="' . $row['unit_price'] . '">';
                echo '<input type="hidden" name="total_payment" value="' . $total_price . '">';
                echo '</div>';
            }
        } else {
            echo "No items selected for checkout.";
        }
    } else {
        // Handle case when no items are selected
        echo "No items selected for checkout.";
    }

    $totalPayment = $merchandiseSubtotal + $shippingFee;
    ?>
    <input type="hidden" name="total_payment" value="<?php echo $totalPayment; ?>">
    <div class="total">
    <p>₱<?php echo number_format($merchandiseSubtotal, 2); ?></p>
        <p>Order Total (<?php echo $result->num_rows; ?> Items):</p>
    </div>
    <div class="invoice">
        <p>E-Invoice</p>
    <i class="bi bi-question-circle"></i>
    <?php
    // Retrieve status from the invoice table based on the customer's email
    $sql_status = "SELECT status FROM invoice WHERE email = ?";
    $stmt_status = $conn->prepare($sql_status);
    $stmt_status->bind_param("s", $_SESSION['email']);
    $stmt_status->execute();
    $result_status = $stmt_status->get_result();

    // Initialize status variable
    $status = "";

    if ($result_status->num_rows > 0) {
        // Fetch status
        $row_status = $result_status->fetch_assoc();
        $status = $row_status['status'];
    }

    // Check if the status is "Completed"
    if ($status === "Completed") {
        // Change the button text to "View Invoice" and "Change"
        echo '<a href="#" class="req-invoice" data-bs-toggle="modal" data-bs-target="#viewInvoiceModal">View Invoice</a>';
    } else {
        // Keep the button text as "Request Now"
        echo '<a href="#" class="req-invoice" data-bs-toggle="modal" data-bs-target="#requestInvoiceModal"
   data-name="' . $name . '"
   data-email="' . $_SESSION['email'] . '"
   data-contact="' . $contact_num . '"
   data-address="' . $address . '">Request Now</a>';
    }
    ?>
    </div>

    <div class="payment">
            <p class=payment-label>Payment Method</p>
            <input type="hidden" name="payment" id="payment" value="<?php echo htmlspecialchars($payment); ?>">
            <select name="payment" class="payment-address" aria-label="Default select example" id="paymentMethod">
        <option <?php if ($payment == "Not selected") echo 'selected'; ?> disabled>Choose Payment Method</option>
        <option value="Gcash" <?php if ($payment == 'Gcash') echo 'selected'; ?>>Gcash</option>
<option value="Cash On Delivery" <?php if ($payment == 'Cash On Delivery') echo 'selected'; ?>>Cash On Delivery</option>
    </select>
    </div>
    <div class="payment-details">
    <div class="merch-total">
        <p>Merchandise Subtotal:</p>
        <p>₱<?php echo number_format($merchandiseSubtotal, 2); ?></p>
    </div>
    <div class="ship-total">
    <p>₱<?php echo number_format($shippingFee, 2); ?></p>
        <p>Shipping Fee:</p>
    </div>
    <div class="total-payment-check">
        <p>Total Payment:</p>
        <p>₱<?php echo number_format($totalPayment, 2); ?></p>
    </div>

    </div>

    <div class="placeorder">
    <button type="submit" name="place_order" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#orderConfirmationModal" id="placeOrderButton">Place Order</button>
        </div>
    </div>

</form>

<script>
document.getElementById('submitInvoiceButton').addEventListener('click', function() {
    console.log('Submit button clicked'); // Log to check if event listener is triggered

    // Retrieve form data
    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;
    var contact_num = document.getElementById('contact_num').value;
    var address = document.getElementById('address').value;

    // Log form data to check if it's retrieved correctly
    console.log('Form data:', name, email, contact_num, address);

    // AJAX request to submit the form data
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'Checkout.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        console.log('Response:', xhr.responseText); // Log response from the server

        if (xhr.status === 200 && xhr.responseText.trim() === 'success') {
            // If the request is successful, close the modal
            var requestInvoiceModal = new bootstrap.Modal(document.getElementById('requestInvoiceModal'));
            requestInvoiceModal.hide();
        } else {
            // Handle errors or display messages
            console.error('Error requesting invoice: ' + xhr.responseText);
        }
    };
    xhr.send('name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email) + '&contact_num=' + encodeURIComponent(contact_num) + '&address=' + encodeURIComponent(address));
});

// JavaScript to populate modal fields with user information
document.addEventListener('DOMContentLoaded', function () {
    var requestInvoiceModal = document.getElementById('requestInvoiceModal');
    requestInvoiceModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var modal = this;
        modal.querySelector('#name').value = button.getAttribute('data-name');
        modal.querySelector('#email').value = button.getAttribute('data-email');
        modal.querySelector('#contact_num').value = button.getAttribute('data-contact');
        modal.querySelector('#address').value = button.getAttribute('data-address');
    });
});

document.getElementById('placeOrderButton').addEventListener('click', function() {
    // Retrieve payment method from the dropdown
    var payment = document.getElementById('paymentMethod').value;
    console.log("Selected payment method:", payment); // Debugging statement
    // Set the payment value in the hidden input field
    document.getElementById('payment').value = payment;

    // Get the current date and format it as "Y-m-d"
    var currentDate = new Date();
    var formattedDate = currentDate.getFullYear() + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + ('0' + currentDate.getDate()).slice(-2);
    // Set the formatted date in the hidden input field
    document.getElementById('order_date').value = formattedDate;

    // Trigger form submission
    document.getElementById('checkoutForm').submit();
});
</script>
    </body>
    </html>
    <?php
    $conn->close(); // Close the connection
    ?>
