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

// Check if request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode the JSON data sent from the client
    $data = json_decode(file_get_contents("php://input"));

    // Extract customerId and updatedContactNumber from the decoded JSON data
    $customerId = $data->customerId;
    $updatedContactNumber = $data->updatedContactNumber;

    // Prepare SQL statement to update contact number
    $sql = "UPDATE customer SET contact_num = :contactNumber WHERE customer_id = :customerId";

    // Prepare and execute the statement
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':contactNumber', $updatedContactNumber, PDO::PARAM_STR);
    $stmt->bindParam(':customerId', $customerId, PDO::PARAM_INT);
    
    // Check if the statement executed successfully
    if ($stmt->execute()) {
        // Return success message as JSON response
        echo json_encode(array('success' => true, 'message' => 'Contact number updated successfully.'));
    } else {
        // Return error message as JSON response
        echo json_encode(array('success' => false, 'message' => 'Error updating contact number.'));
    }
} else {
    // Return error message if request method is not POST
    echo json_encode(array('success' => false, 'message' => 'Invalid request method.'));
}
?>
