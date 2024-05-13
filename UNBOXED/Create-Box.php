<?php
// Increase PHP settings for file uploads
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');
ini_set('max_execution_time', 300); // 300 seconds

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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $field = $_POST['field'];
    $skills = isset($_POST['skills']) ? implode(', ', $_POST['skills']) : '';
    $email = $_POST['inputEmail4'];
    $password = $_POST['inputPassword4'];
    $address = $_POST['validationTooltip03'];

    // Process image upload
    $targetDir = "./images/";
    $targetFile = $targetDir . basename($_FILES["customFile"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file is selected
    if (empty($_FILES["customFile"]["tmp_name"])) {
        $targetFile = ""; // If no file is selected, set empty string
    } else {
        // Check file size
        if ($_FILES["customFile"]["size"] > 20971520) { // 20MB in bytes
            echo "Sorry, your file is too large. Please upload a file smaller than 20MB.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["customFile"]["tmp_name"], $targetFile)) {
            // Insert data into database
            $sql = "INSERT INTO customer (image_dp, name, field, skill, email, password, address)
            VALUES ('$targetFile', '$firstName $lastName', '$field', '$skills', '$email', '$password', '$address')";

            if ($conn->query($sql) === TRUE) {
                // Redirect to login page
                header("Location: login.php");
                exit(); // Ensure script execution stops after redirection
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Close database connection
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

                <label for="firstName" class="firstname">HELLO! MY NAME IS <input type="text" id="firstName" name="firstName" placeholder="FIRST NAME" required>
                <input type="text" id="lastName" name="lastName" placeholder="LAST NAME" required></label>
                <label for="field" class="label0">I AM A</label>
                <select name="field" id="field" onchange="showOtherField()" required>
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
                <label for="skills" class="skill-label">WHAT MAKES ME UNIQUE ARE THE SKILLS I HAVE SUCH AS:</label>
                <div class="skills-checkboxes">
                <label><input type="checkbox" name="skills[]" value="Graphic Design"> Graphic Design</label>
                <label><input type="checkbox" name="skills[]" value="UI/UX Design"> UI/UX Design</label>
                <label><input type="checkbox" name="skills[]" value="Copywriting"> Copywriting</label>
                <label><input type="checkbox" name="skills[]" value="Creative Writing"> Creative Writing</label>
                <label><input type="checkbox" name="skills[]" value="Web Development"> Web Development</label>
                <label><input type="checkbox" name="skills[]" value="Software Development"> Software Development</label>
                <label><input type="checkbox" name="skills[]" value="Project Management"> Project Management</label>
                <label><input type="checkbox" name="skills[]" value="Financial Management"> Financial Management</label>
                <label><input type="checkbox" name="skills[]" value="Painting"> Painting</label>
                <label><input type="checkbox" name="skills[]" value="Drawing"> Drawing</label>
                <label><input type="checkbox" name="skills[]" value="Digital Art"> Digital Art</label>
                <label><input type="checkbox" name="skills[]" value="Event Photography"> Event Photography</label>
                <label><input type="checkbox" name="skills[]" value="Fashion Photography"> Fashion Photography</label>
                <label><input type="checkbox" name="skills[]" value="Content Creation"> Content Creation</label>
                <label><input type="checkbox" name="skills[]" value="Public Speaking"> Public Speaking</label>
                <label><input type="checkbox" name="skills[]" value="Sports Training"> Sports Training</label>
                <label><input type="checkbox" name="skills[]" value="Culinary Skills"> Culinary Skills</label>
                </div>
               
                <form>
  <div class="form-row">
    <div class="form-group col-md-5">
      <label for="inputEmail4">Email</label>
      <input type="email" class="form-control" id="inputEmail4" name="inputEmail4" placeholder="example@gmail.com">
    </div>
    <div class="form-group col-md-6">
      <label for="inputPassword4">Password</label>
      <input type="password" class="form-control" id="inputPassword4" name="inputPassword4" placeholder="Password">
    </div>
  </div>
  <div class="form-row">
    <div class="col-md-6 mb-3">
      <label for="validationTooltip03">Address</label>
      <input type="text" class="form-control-address" id="validationTooltip03" name="validationTooltip03" placeholder="Address" required>
    </div>

</form>
                <button type="submit" id="submitButton">Create</button>
        </div>
    </div>

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
    </script>
</body>
</html>