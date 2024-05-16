<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $shipment_date = $_POST['shipping_date'];
    $shipment_time = $_POST['pick_up_time'];
    $user_address = $_POST['pickup_address'];
    $shipping_company = $_POST['shipping-company'];
    $order_item_id = $_POST['order-item-id']; // Added to retrieve order_item_id

    // Validate and sanitize form data if necessary

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

    // Prepare SQL statement to insert data into the shippings table
    $sql_insert = "INSERT INTO shippings (shipping_date, pick_up_time, pickup_address, shipping_company) 
            VALUES ('$shipment_date', '$shipment_time', '$user_address', '$shipping_company')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "New record created successfully";

        // Update the status of the order_item to "To Ship Return"
        $sql_update = "UPDATE order_item SET status = 'To Ship Return' WHERE order_item_id = $order_item_id";

        if ($conn->query($sql_update) === TRUE) {
            echo "Order item status updated successfully";
        } else {
            echo "Error updating order item status: " . $conn->error;
        }
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }

    // Close database connection
    $conn->close();
}
?>
