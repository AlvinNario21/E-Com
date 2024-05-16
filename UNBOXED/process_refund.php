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

// Get data from AJAX POST request
$order_id = $_POST['order_id'];
$request_date = $_POST['request_date'];
$reason = $_POST['reason'];

// Retrieve customer_id and customer_name from session
$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['name'];

// Update order item status to "Request Return"
$updateOrderItemQuery = "UPDATE order_item SET status = 'Request Return' WHERE order_id = '$order_id'";
$updateResult = mysqli_query($conn, $updateOrderItemQuery);

// Insert refund request into return_refund_requests table
$insertRefundRequestQuery = "INSERT INTO return_refund_requests (order_id, customer_id, customer_name, request_date, reason) 
                            VALUES ('$order_id', '$customer_id', '$customer_name', '$request_date', '$reason')";
$insertResult = mysqli_query($conn, $insertRefundRequestQuery);

// Check if both queries were successful
if ($updateResult && $insertResult) {
    // Return success response
    echo "Success";
} else {
    // Return error response
    http_response_code(500);
    echo "Error processing refund request. Please try again.";
}

// Close database connection
mysqli_close($conn);
?>