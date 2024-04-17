<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "unboxed"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle file upload
function uploadFile($file) {
    $target_dir = "./images/";
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check file size
    if ($file["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "The file ". htmlspecialchars( basename( $file["name"])). " has been uploaded.";
            return $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Function to insert data into database
function insertData($conn, $showcase_name, $customer_id, $showcase_dp) {
    $sql = "INSERT INTO showcase (showcase_name, customer_id, showcase_dp) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $showcase_name, $customer_id, $showcase_dp);
    
    if ($stmt->execute() === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $showcase_name = $_POST["showcase_name"];
    $customer_id = $_POST["customer_id"];
    $showcase_dp = uploadFile($_FILES["showcase_dp"]);

    // Insert data into database
    insertData($conn, $showcase_name, $customer_id, $showcase_dp);
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<body>

<form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Showcase Name: <input type="text" name="showcase_name"><br>
    Customer ID: <input type="text" name="customer_id"><br>
    Showcase Image: <input type="file" name="showcase_dp"><br>
    <input type="submit" value="Submit">
</form>

</body>
</html>
