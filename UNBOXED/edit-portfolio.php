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

// Check if the user is logged in
if (isset($_SESSION["email"])) {
    // User is logged in, fetch the user's name, image_dp, and customer_id from the session
    $name = $_SESSION["name"]; // Assuming 'name' is the column name for user's name
    $image_dp = $_SESSION["image_dp"]; // Assuming 'image_dp' is the column name for user's image
    $loggedInCustomerID = $_SESSION["customer_id"]; // Assuming 'customer_id' is the column name for user's customer_id
} else {
    // User is not logged in, set default values
    $name = "";
    $image_dp = "";
    $loggedInCustomerID = null;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve form data
    $name = $_POST['name'];
    $field = $_POST['field'];
    $skills = isset($_POST['skills']) ? implode(',', $_POST['skills']) : ''; // Convert array of skills to comma-separated string
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contact_num = $_POST['contact_num'];
    $customer_id = $_SESSION['customer_id'];

    // Validate form data (you can add more validation as needed)
    if (!empty($name) && !empty($field) && !empty($email) && !empty($address) && !empty($contact_num)) {
        // Prepare update statement
        $sql = "UPDATE customer SET name=?, field=?, skill=?, email=?, address=?, contact_num=? WHERE customer_id=?"; // Assuming your customer table has an 'id' column
        
        // Prepare and execute the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $field, $skills, $email, $address, $contact_num, $customer_id); // Assuming $customer_id is the ID of the customer whose data is being updated
        $stmt->execute();

        // Check if update was successful
        if ($stmt->affected_rows > 0) {
            echo "<script>window.location.href = 'Profile.php?customer_id=$customer_id&name=$name&image_dp=$image_dp';</script>";
            exit();
        } else {
            echo "Error updating portfolio information: " . $conn->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }

    // Close connection
    $conn->close();
}
?>