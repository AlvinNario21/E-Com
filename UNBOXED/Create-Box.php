<?php
// Database connection parameters
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $field = htmlspecialchars($_POST['field']);
    $otherField = isset($_POST['otherFieldInput']) ? htmlspecialchars($_POST['otherFieldInput']) : null;
    $email = htmlspecialchars($_POST['inputEmail4']);
    $password = htmlspecialchars($_POST['inputPassword4']); // Store password as plain text
    $address = htmlspecialchars($_POST['validationTooltip03']);
    $contactNumber = htmlspecialchars($_POST['contact']);

    // Handle file upload
    $target_dir = "./images/";
    $target_file = $target_dir . basename($_FILES["customFile"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $uploadOk = 1;

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["customFile"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($_FILES["customFile"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["customFile"]["tmp_name"], $target_file)) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO customer (image_dp, name, field, email, password, address, contact_num) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $target_file, $name, $field, $email, $password, $address, $contactNumber);

            $name = $firstName . ' ' . $lastName;
            if ($otherField) {
                $field = $otherField;
            }

            if ($stmt->execute()) {
                // Successful creation: show alert and redirect
                echo "<script>
                        alert('New record created successfully');
                        window.location.href = 'login.php';
                      </script>";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Box</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="create-box-css.css?v=<?php echo time(); ?>">
</head>
<body>
<nav class="backbtn">
    <a href="javascript:history.go(-1);"><i class="bi bi-chevron-left"></i></a>
</nav>

<div class="logo">
    <img src="./images/logo.png" alt="Logo">
    <h1>"TIME TO GET OUT OF THE BOX"</h1>
    <h3>Fill out the box...</h3>
    <div class="container">
        <form id="createForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="custom-file">
                <img src="" class="profile-pic" alt="">
                <input type="file" class="custom-file-input" id="customFile" name="customFile" onchange="displayImage(this)">
                <label class="custom-file-label" for="customFile">Profile</label>
            </div>

            <label for="firstName" class="firstname">HELLO! MY NAME IS 
                <input type="text" id="firstName" name="firstName" placeholder="FIRST NAME" required>
                <input type="text" id="lastName" name="lastName" placeholder="LAST NAME" required>
            </label>

            <label for="field" class="label0">I AM A</label>
            <select name="field" id="field" onchange="showOtherField(); loadSkills();" required>
                <option value="">CHOOSE A FIELD</option>
                <option value="Designer">Designer</option>
                <option value="Developer">Developer</option>
                <option value="Writer">Writer</option>
                <option value="Entrepreneur">Entrepreneur</option>
                <option value="Freelancer">Freelancer</option>
                <option value="Artist">Artist</option>
                <option value="Photographer">Photographer</option>
                <option value="Influencer">Influencer</option>
                <option value="Athlete">Athlete</option>
                <option value="Chef">Chef</option>
                <option value="other">Other</option>
            </select>

            <div id="otherField" style="display: none;">
                <label for="otherFieldInput">Please specify:</label>
                <input type="text" id="otherFieldInput" name="otherFieldInput">
            </div>

            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="inputEmail4">Email</label>
                    <input type="email" class="form-control" id="inputEmail4" name="inputEmail4" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="inputPassword4">Password</label>
                    <input type="password" class="form-control" id="inputPassword4" name="inputPassword4" placeholder="Password" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="validationTooltip03">Address</label>
                    <input type="text" class="form-control" id="validationTooltip03" name="validationTooltip03" placeholder="Address" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="contact">Contact Number</label>
                    <input type="text" class="form-control" id="contact" name="contact" placeholder="09123456789" required>
                </div>
            </div>
            <button type="submit" id="submitButton">Create</button>
        </form>
    </div>
</div>
</body>
<script>
function showOtherField() {
    var fieldDropdown = document.getElementById("field");
    var otherFieldDiv = document.getElementById("otherField");
    if (fieldDropdown.value === "other") {
        otherFieldDiv.style.display = "block";
        document.getElementById("otherFieldInput").setAttribute("required", "true");
    } else {
        otherFieldDiv.style.display = "none";
        document.getElementById("otherFieldInput").removeAttribute("required");
    }
}

// Event listener for form submission
document.getElementById("createForm").addEventListener("submit", function(event) {
    var firstName = document.getElementById("firstName").value;
    var lastName = document.getElementById("lastName").value;
    var field = document.getElementById("field").value;

    if (!firstName || !lastName || !field) {
        event.preventDefault(); // Prevent form submission if any required field is empty
        alert("Please fill out all required fields.");
    } else {
        // If all required fields are filled, let the form submit
    }
});

function displayImage(input) {
    var file = input.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = input.parentElement.querySelector('.profile-pic');
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
</script>
</html>