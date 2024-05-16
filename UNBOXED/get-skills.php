<?php
// Establish a connection to your database (replace placeholders with actual values)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "unboxed";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch skills based on the selected field
if (isset($_GET['field'])) {
    $field = $_GET['field'];
    
    $sql = "SELECT skills FROM field WHERE field_name = '$field'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $skills = "";
        while ($row = $result->fetch_assoc()) {
            $skills = explode(",", $row["skills"]);
        }
        foreach ($skills as $skill) {
            echo '<label><input type="checkbox" class="skill-check" name="skills[]" value="' . $skill . '"> ' . $skill . '</label>';
        }
    } else {
        echo "No skills found for this field";
    }
}
?>