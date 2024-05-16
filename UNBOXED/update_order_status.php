<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_item_id = $_POST['order_item_id'];
    $status = $_POST['status'];

    // Validate and sanitize input data if necessary

    // Connect to your database (Replace with your database credentials)
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

    // Update the status of the order_item
    $sql_update = "UPDATE order_item SET status = '$status' WHERE order_item_id = $order_item_id";

    if ($conn->query($sql_update) === TRUE) {
        echo "Order item status updated successfully";
    } else {
        echo "Error updating order item status: " . $conn->error;
    }

    // Close database connection
    $conn->close();
}
?>