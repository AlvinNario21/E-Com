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

if(isset($_POST['term'])){
    $term = $_POST['term'];
    // Example query, replace with your own
    $query = "SELECT * FROM skills WHERE skill_name LIKE '%$term%'";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            echo "<li>" . $row['skill_name'] . "</li>";
        }
    } else {
        echo "<li>No skills found</li>";
    }
}
?>
