<?php
// Assuming you have a database connection established already
session_start();

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

if(isset($_POST['newAddress']) && isset($_SESSION['customer_id'])) {
    $newAddress = $_POST['newAddress'];
    $customerId = $_SESSION['customer_id'];

    // Perform the database update
    $query = "UPDATE customer SET address = ? WHERE customer_id = ?";
    $statement = $conn->prepare($query);
    $statement->bind_param("si", $newAddress, $customerId);
    $statement->execute();

    if($statement->affected_rows > 0) {
        echo "success"; // Return success upon successful address update
    } else {
        echo "Failed to update address.";
    }
} else {
    echo "Invalid request.";
}
?>