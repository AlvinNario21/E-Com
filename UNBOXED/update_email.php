<?php
session_start(); // Start the session

// Connect to database (replace these values with your actual database credentials)
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

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if customer ID is set in the session and if new email is received
    if (isset($_SESSION["customer_id"]) && isset($_POST["newEmail"])) {
        $customerId = $_SESSION["customer_id"];
        $newEmail = $_POST["newEmail"];

        // Validate email address
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array("success" => false, "error" => "Invalid email address"));
        } else {
            // Prepare SQL statement with parameterized query
            $sql = "UPDATE customer SET email = ? WHERE customer_id = ?";
            $stmt = $conn->prepare($sql);
            
            // Bind parameters and execute query
            $stmt->bind_param("si", $newEmail, $customerId);
            if ($stmt->execute()) {
                echo json_encode(array("success" => true));
            } else {
                echo json_encode(array("success" => false, "error" => "Error updating email: " . $stmt->error));
            }

            $stmt->close();
        }
    } else {
        echo json_encode(array("success" => false, "error" => "Customer ID or new email not received"));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Invalid request method"));
}

$conn->close();
?>