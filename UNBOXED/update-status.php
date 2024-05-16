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

// Retrieve order_id and new_status from AJAX POST request
$order_id = $_POST['order_id'];
$new_status = $_POST['new_status'];

// Update the order status in the database
$updateOrderStatusQuery = "UPDATE order_item SET status = '$new_status' WHERE order_id = '$order_id'";
$result = mysqli_query($conn, $updateOrderStatusQuery);

// Check if the query was successful
if($result) {
    // Return success response
    echo "Success";
} else {
    // Return error response
    http_response_code(500);
    echo "Error updating order status. Please try again.";
}
// Close database connection
mysqli_close($conn);
?>
