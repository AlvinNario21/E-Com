<?php
// Start session to access session variables
session_start();

// Check if customer_id is set in the session
if(isset($_SESSION['customer_id'])) {
    // Get the customer_id from the session
    $customerId = $_SESSION['customer_id'];

    // Check if selectedSkills is set in POST data
    if(isset($_POST['selectedSkills'])) {
        // Get selected skills from POST data
        $selectedSkills = $_POST['selectedSkills'];

        // Convert selected skills array to a comma-separated string
        $skillString = implode(",", $selectedSkills);

        // Connect to your database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "unboxed";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update customer table with selected skills
        $sql = "UPDATE customer SET skill = '$skillString' WHERE customer_id = $customerId";

        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            echo "Error updating skills: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "No selected skills received";
    }
} else {
    echo "Customer ID not found in session";
}
?>
