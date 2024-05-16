<?php
session_start(); // Start session to manage user login state


// Check if the logout button is clicked
if (isset($_GET['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session 
    session_destroy();

    // Redirect to the login page
    header("Location: index.php");
    exit;
}// Start session to manage user login state

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

// Check if the user is logged in and has a customer_id stored in the session
if(isset($_SESSION['customer_id']) && isset($_POST['newName'])) {
    $customer_id = $_SESSION['customer_id'];
    $newName = $_POST['newName'];
    $customer_name = $_SESSION['name'];

    // Update name in the database for relevant tables based on customer_id
    $stmt1 = $conn->prepare("UPDATE customer SET name = ? WHERE customer_id = ?");
    $stmt1->bind_param("si", $newName, $customer_id);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("UPDATE invoice SET name = ? WHERE name = ?");
    $stmt2->bind_param("si", $newName, $customer_id);
    $stmt2->execute();
    $stmt2->close();

    $stmt3 = $conn->prepare("UPDATE product SET owner = ? WHERE customer_id = ?");
    $stmt3->bind_param("si", $newName, $customer_id);
    $stmt3->execute();
    $stmt3->close();

    $stmt4 = $conn->prepare("UPDATE showcase SET owner = ? WHERE customer_id = ?");
    $stmt4->bind_param("si", $newName, $customer_id);
    $stmt4->execute();
    $stmt4->close();

    $stmt5 = $conn->prepare("UPDATE order_item SET product_owner = ? WHERE customer_id = ?");
    $stmt5->bind_param("si", $newName, $customer_id);
    $stmt5->execute();
    $stmt5->close();

    $stmt6 = $conn->prepare("UPDATE transaction SET product_owner = ? WHERE product_owner = ?");
    $stmt6->bind_param("si", $newName, $customer_id);
    $stmt6->execute();
    $stmt6->close();

    // Check if queries were successful and return response accordingly
    if($stmt1 && $stmt2 && $stmt3 && $stmt4 && $stmt5 && $stmt6) {
        echo "Portfolio name updated successfully!";
    } else {
        echo "Error updating portfolio name.";
    }
} else {
    echo "No data received or user not logged in.";
}

// Close connection
$conn->close();
?>