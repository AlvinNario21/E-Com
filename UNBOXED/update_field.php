<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    // Redirect the user to the login page if not logged in
    header("Location: login.php");
    exit();
}

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

// Retrieve the customer_id from the session
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
} else {
    // Handle case when customer_id is not set
    echo "Customer ID not found.";
    exit; // Exit script if customer_id is not set
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $customer_id = $_SESSION['customer_id'];
    $field = $_POST['field'];
    
    // Update the field in the database
    $query = "UPDATE customer SET field = '$field' WHERE customer_id = '$customer_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        // Success response
        echo "Field updated successfully!";
    } else {
        // Error response
        echo "Error updating field: " . mysqli_error($conn);
    }
}
?>