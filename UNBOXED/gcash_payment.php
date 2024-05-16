<?php
// Start session
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "unboxed";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Retrieve the total payment from the GET parameter
$total_payment = isset($_GET['total_payment']) ? $_GET['total_payment'] : 0;
$payment = isset($_GET['payment']) ? $_GET['payment'] : '';
$product_owner = isset($_GET['product_owner']) ? $_GET['product_owner'] : '';
$product_name = isset($_GET['product_name']) ? $_GET['product_name'] : '';

// Check if form is submitted
if(isset($_POST['submit_payment'])) {
    // Retrieve data from form
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];
    $product_id = $_POST['product_id'];
    $product_img = $_POST['product_img'];
    $product_name = $_POST['product_name'];
    $product_owner = $_POST['product_owner'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $total_payment = $_POST['total_payment'];
    $payment = $_POST['payment'];
    $order_date = date('Y-m-d'); // Current date
    $status = $_POST['status'];

    // Generate random order_id, transaction_id, and reference_id
    $order_id = uniqid('');
    $transaction_id = uniqid('');
    $reference_id = uniqid('');

    // Insert data into order_item table
    $sql_order_item = "INSERT INTO order_item (order_id, customer_id, customer_name, address, contact, product_id, product_img, product_name, product_owner, quantity, price, total_payment, payment, order_date, status)
            VALUES ('$order_id', '$customer_id', '$customer_name', '$address', '$contact', '$product_id', '$product_img', '$product_name', '$product_owner', '$quantity', '$price', '$total_payment', '$payment', '$order_date', 'Pending')";

    if ($conn->query($sql_order_item) === TRUE) {
        // Delete product from cart_items
        $sql_delete_cart = "DELETE FROM cart_items WHERE product_id = '$product_id'";

        if ($conn->query($sql_delete_cart) === TRUE) {
            // Insert data into transaction table with generated IDs and payment method
            // Insert data into transaction table with generated IDs, payment method, and status as "Completed"
            $sql_transaction = "INSERT INTO transaction (transaction_id, order_id, name, contact, total_amount, payment_method, reference_id, product_owner, product_name, order_date, status)
            VALUES ('$transaction_id', '$order_id', '$customer_name', '$contact', '$total_payment', '$payment', '$reference_id', '$product_owner', '$product_name', '$order_date', 'Completed')";

            if ($conn->query($sql_transaction) === TRUE) {
                // Update wallet amount in the customer table if product_owner matches customer_name
                $sql_update_wallet = "UPDATE customer SET wallet = wallet + '$total_payment' WHERE name = '$product_owner'";
                if ($conn->query($sql_update_wallet) === TRUE) {
                    // Redirect to Purchase.php
                    header("Location: Purchase.php");
                    exit();
                } else {
                    echo "Error updating wallet: " . $conn->error;
                }
            } else {
                echo "Error inserting into transaction table: " . $conn->error;
            }
        } else {
            echo "Error deleting from cart_items: " . $conn->error;
        }
    } else {
        echo "Error inserting into order_item table: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gcash Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script>
        // JavaScript function to display an alert after successful payment
        function showConfirmation() {
            var name = document.getElementById("name").value;
            var totalPayment = document.getElementById("total_payment").value;
            if (name && totalPayment) {
                alert("Payment successful! \nName: " + name + "\nTotal Payment: " + totalPayment);
            } else {
                alert("Payment successful!");
            }
        }
    </script>
</head>
<body>
<div class="gcash-container">
    <div class="gcash-form">
    <h2 class="gcash-label">Gcash Payment Form</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label class="gcash" for="name">Name:</label>
            <input type="text" id="name" class="gcash-input" name="customer_name" required>
        </div>
        <div>
            <label class="gcash" for="contact">Contact Number:</label>
            <input type="text" class="gcash-input" id="contact" name="contact" required>
        </div>
        <div>
            <label class="gcash" for="total_amount">Total Amount:</label>
            <input type="text" id="total_payment" class="gcash-input" name="total_payment" value="<?php echo htmlspecialchars($total_payment); ?>" readonly>
        </div>
        <div>
            <label class="gcash" for="payment_method">Payment Method:</label>
            <input type="text" id="payment" class="gcash-input" name="payment" value="<?php echo htmlspecialchars($payment); ?>" readonly>
        </div>
        <!-- Add hidden input fields for other data -->
        <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($_GET['customer_id']); ?>">
        <input type="hidden" name="address" value="<?php echo htmlspecialchars($_GET['address']); ?>">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($_GET['product_id']); ?>">
        <input type="hidden" name="product_img" value="<?php echo htmlspecialchars($_GET['product_img']); ?>">
        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($_GET['product_name']); ?>">
        <input type="hidden" name="product_owner" value="<?php echo htmlspecialchars($_GET['product_owner']); ?>">
        <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($_GET['quantity']); ?>">
        <input type="hidden" name="price" value="<?php echo htmlspecialchars($_GET['price']); ?>">
        <input type="hidden" name="payment" value="<?php echo htmlspecialchars($_GET['payment']); ?>">
        <input type="hidden" name="order_date" value="<?php echo htmlspecialchars($_GET['order_date']); ?>">
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($_GET['status']); ?>">
        <a href="Cart.php" style="text-decoration: none;"><input type="button" name="cancel" id="cancel" class="cancel-btn" value="Cancel"></a>
        <input type="submit" name="submit_payment" id="submit_payment" class="gcash-btn" value="Submit Payment" onclick="showConfirmation();">
    </form>
    </div>
    </div>
</body>
</html>