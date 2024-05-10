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
}

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

// Retrieve the showcase_name from the URL parameter
if (isset($_GET['showcase_name'])) {
    $showcase_name = $_GET['showcase_name'];
} else {
    $showcase_name = ""; // Default value if showcase_name is not provided
}

// Fetch the customer ID based on the provided name (if available) or use the logged-in user's customer ID
if (isset($_GET['customer_id'])) {
    $customerID = $_GET['customer_id'];
} else {
    $customerID = $loggedInCustomerID;
}

// Fetch the customer name based on the provided customer ID
if (!empty($customerID)) {
    $customer_query = "SELECT name FROM customer WHERE customer_id = $customerID";
    $customer_result = $conn->query($customer_query);

    if ($customer_result->num_rows > 0) {
        // Fetch the customer's name
        $customer_row = $customer_result->fetch_assoc();
        $customerName = $customer_row['name'];
    } else {
        $customerName = ""; // Default value if customer name is not found
    }
} else {
    $customerName = ""; // Default value if customer ID is not provided
}

// Check for delete success or error messages
if (isset($_SESSION['delete_success']) && $_SESSION['delete_success']) {
    echo '<script>alert("Showcase deleted successfully.");</script>';
    unset($_SESSION['delete_success']); // Clear the session variable
} elseif (isset($_SESSION['delete_error'])) {
    echo '<script>alert("Error deleting showcase: ' . $_SESSION['delete_error'] . '");</script>';
    unset($_SESSION['delete_error']); // Clear the session variable
}

// Check if the icon is clicked for showcase deletion
if (isset($_POST['delete_showcase'])) {
    // Check if all required parameters are provided
    if (isset($_POST['customer_id']) && isset($_POST['showcase_name'])) {
        // Retrieve customer ID and showcase name
        $customerID = $_POST['customer_id'];
        $showcaseName = $_POST['showcase_name'];

        // Prepare and execute SQL statement to delete the showcase
        $stmt = $conn->prepare("DELETE FROM showcase WHERE customer_id = ? AND showcase_name = ?");
        $stmt->bind_param("is", $customerID, $showcaseName);

        if ($stmt->execute()) {
            // Showcase deleted successfully
            echo '<script>alert("Showcase deleted successfully.");</script>';
        } else {
            // Error deleting showcase
            echo '<script>alert("Error deleting showcase: ' . $conn->error . '");</script>';
        }

        // Close statement
        $stmt->close();
        
        // Redirect to the same page to prevent resubmission
        header("Location: Profile.php");
        exit;
    } else {
        // Required parameters not provided
        echo '<script>alert("Missing parameters for showcase deletion.");</script>';
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_showcase'])) {
    // Check if the required form fields are set
    if (isset($_POST['showcase_name'], $_FILES['showcase_dp']['name'], $_POST['customer_id'], $_POST['owner'])) {
        // Retrieve form data
        $showcaseName = $_POST['showcase_name'];
        $showcaseImageName = $_FILES['showcase_dp']['name']; // Name of the uploaded file
        $customerID = $_POST['customer_id'];
        $customerName = $_POST['owner'];

        // Validate the data (perform more thorough validation as needed)
        if (empty($showcaseName) || empty($customerID) || empty($customerName) || empty($showcaseImageName)) {
            echo "Please fill in all the fields.";
        } else {
            // Prepare and bind the INSERT statement
            $stmt = $conn->prepare("INSERT INTO showcase (showcase_name, showcase_dp, customer_id, owner) VALUES (?, ?, ?, ?)");

            if (!$stmt) {
                // Handle preparation error
                die("Error preparing statement: " . $conn->error);
            }

            // Combine directory path with image name
            $targetDirectory = "./images/";
            $showcaseImage = $targetDirectory . $showcaseImageName;

            // Bind parameters
            $stmt->bind_param("ssis", $showcaseName, $showcaseImage, $customerID, $customerName);

            if (!$stmt) {
                // Handle binding error
                die("Error binding parameters: " . $stmt->error);
            }

            // Execute the statement
            if ($stmt->execute()) {
                echo '<script>alert("Showcase added successfully.");</script>';
                
                // Move uploaded file to directory
                $targetFile = $targetDirectory . basename($_FILES["showcase_dp"]["name"]);
                if (move_uploaded_file($_FILES["showcase_dp"]["tmp_name"], $targetFile)) {

                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo "Error adding showcase: " . $stmt->error;
            }

            // Close statement
            $stmt->close();
        }
    } else {
        echo "";
    }
}

// Check if the form for editing showcase is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_showcase_name'])) {
    // Retrieve form data
    $editShowcaseName = $_POST['edit_showcase_name'];
    $originalShowcaseName = $_POST['original_showcase_name'];
    $editCustomerID = $_POST['edit_customer_id'];
    
    // Check if a new image is uploaded
    if (!empty($_FILES['edit_showcase_dp']['name'])) {
        $editShowcaseImageName = $_FILES['edit_showcase_dp']['name']; // Name of the uploaded file
        
        // Perform validation on the uploaded file (size, type, etc.)

        // Move uploaded file to directory
        $targetDirectory = "./images/";
        $editShowcaseImage = $targetDirectory . $editShowcaseImageName;
        move_uploaded_file($_FILES["edit_showcase_dp"]["tmp_name"], $editShowcaseImage);

        // Update showcase with both name and image
        $updateShowcaseQuery = "UPDATE showcase SET showcase_name = ?, showcase_dp = ? WHERE customer_id = ? AND showcase_name = ?";
        $stmt = $conn->prepare($updateShowcaseQuery);
        $stmt->bind_param("ssis", $editShowcaseName, $editShowcaseImage, $editCustomerID, $originalShowcaseName);
    } else {
        // Update showcase with only name
        $updateShowcaseQuery = "UPDATE showcase SET showcase_name = ? WHERE customer_id = ? AND showcase_name = ?";
        $stmt = $conn->prepare($updateShowcaseQuery);
        $stmt->bind_param("sis", $editShowcaseName, $editCustomerID, $originalShowcaseName);
    }
    
    if (!$stmt->execute()) {
        // Error updating showcase
        echo '<script>alert("Error updating showcase: ' . $stmt->error . '");</script>';
        $stmt->close();
        exit(); // Exit script if showcase update fails
    }
    
    // Close statement
    $stmt->close();
    
    // Update showcase name in product table
    $updateProductShowcaseNameQuery = "UPDATE product SET showcase_name = ? WHERE customer_id = ? AND showcase_name = ?";
    $stmtProduct = $conn->prepare($updateProductShowcaseNameQuery);
    $stmtProduct->bind_param("sis", $editShowcaseName, $editCustomerID, $originalShowcaseName);
    
    if (!$stmtProduct->execute()) {
        // Error updating showcase name in product
        echo '<script>alert("Error updating showcase name in product: ' . $stmtProduct->error . '");</script>';
        $stmtProduct->close();
        exit(); // Exit script if product showcase name update fails
    }
    
    // Close product showcase name update statement
    $stmtProduct->close();
    
    // Redirect back to the same page
    echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
}


// Check if a file is uploaded
if(isset($_FILES['image'])) {
    // File details
    $file_name = $_FILES['image']['name'];
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_destination = "./images/" . $file_name;

    // Move uploaded file to desired location
    if(move_uploaded_file($file_tmp, $file_destination)) {
        // File moved successfully
        // Update image_dp field in the database with the new file name
        $customer_id = $_SESSION['customer_id']; // Assuming customer ID is stored in session
        $new_image_dp = $file_destination;
        $update_query = "UPDATE customer SET image_dp = '$new_image_dp' WHERE customer_id = $customer_id";

        if ($conn->query($update_query) === TRUE) {
            // Update session variable with the new profile image path
            $_SESSION['image_dp'] = $new_image_dp;
            // Image_dp updated successfully
            echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
        } else {
            // Error updating image_dp
            echo "Error updating profile image: " . $conn->error;
        }
    } else {
        // Failed to move file
        echo "Error uploading file.";
    }
}

// Check if the form for updating name is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_name'])) {
    // Retrieve the new name from the form
    $newName = $_POST['new_name'];
    
    // Update the name in the customer table
    $updateNameQuery = "UPDATE customer SET name = ? WHERE customer_id = ?";
    $stmtName = $conn->prepare($updateNameQuery);
    
    if (!$stmtName) {
        // Handle preparation error
        die("Error preparing name update statement: " . $conn->error);
    }

    // Bind parameters for name update
    $stmtName->bind_param("si", $newName, $loggedInCustomerID);

    if (!$stmtName) {
        // Handle binding error
        die("Error binding name update parameters: " . $stmtName->error);
    }

    // Execute the statement for name update
    if (!$stmtName->execute()) {
        // Error updating name
        echo '<script>alert("Error updating name: ' . $stmtName->error . '");</script>';
        $stmtName->close();
        exit(); // Exit script if name update fails
    }

    // Close name update statement
    $stmtName->close();

    // Update session name
    $_SESSION['name'] = $newName;

    // Redirect back to the same page
    echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";

    // Update the owner in the showcase database
    $updateOwnerShowcaseQuery = "UPDATE showcase SET owner = ? WHERE customer_id = ?";
    $stmtOwnerShowcase = $conn->prepare($updateOwnerShowcaseQuery);
    
    if (!$stmtOwnerShowcase) {
        // Handle preparation error
        die("Error preparing owner update statement for showcase: " . $conn->error);
    }

    // Bind parameters for showcase owner update
    $stmtOwnerShowcase->bind_param("si", $newName, $loggedInCustomerID);

    if (!$stmtOwnerShowcase) {
        // Handle binding error
        die("Error binding owner update parameters for showcase: " . $stmtOwnerShowcase->error);
    }

    // Execute the statement for showcase owner update
    if (!$stmtOwnerShowcase->execute()) {
        // Error updating owner in showcase
        echo '<script>alert("Error updating owner in showcase: ' . $stmtOwnerShowcase->error . '");</script>';
        $stmtOwnerShowcase->close();
        exit(); // Exit script if showcase owner update fails
    }

    // Close owner update statement for showcase
    $stmtOwnerShowcase->close();

    // Update the owner in the product table
    $updateOwnerProductQuery = "UPDATE product SET owner = ? WHERE customer_id = ?";
    $stmtOwnerProduct = $conn->prepare($updateOwnerProductQuery);
    
    if (!$stmtOwnerProduct) {
        // Handle preparation error
        die("Error preparing owner update statement for product: " . $conn->error);
    }

    // Bind parameters for product owner update
    $stmtOwnerProduct->bind_param("si", $newName, $loggedInCustomerID);

    if (!$stmtOwnerProduct) {
        // Handle binding error
        die("Error binding owner update parameters for product: " . $stmtOwnerProduct->error);
    }

    // Execute the statement for product owner update
    if (!$stmtOwnerProduct->execute()) {
        // Error updating owner in product
        echo '<script>alert("Error updating owner in product: ' . $stmtOwnerProduct->error . '");</script>';
        $stmtOwnerProduct->close();
        exit(); // Exit script if product owner update fails
    }

    // Close owner update statement for product
    $stmtOwnerProduct->close();

    // Redirect back to the same page
    echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
}

// Check if the premium data is sent via POST
if(isset($_POST['premium'])) {
    // Sanitize the input
    $premium = $_POST['premium'];
    
    // Assuming you have a session variable containing the logged-in user's customer_id
    session_start();
    if(isset($_SESSION['customer_id'])) {
        $customer_id = $_SESSION['customer_id'];
        
        // Update the premium column in the customer table for the logged-in user
        $query = "UPDATE customer SET premium = '$premium' WHERE customer_id = '$customer_id'";
        
        // Assuming $conn is your database connection
        if(mysqli_query($conn, $query)) {
            // If update is successful, return success message
            echo "Premium updated successfully";
        } else {
            // If there's an error, return error message
            echo "Error updating premium: " . mysqli_error($conn);
        }
    } else {
        // If session variable containing customer_id is not set, return error message
        echo "Customer ID not found in session";
    }
} else {
    // If premium data is not sent, return error message
    echo "";
}

// Check if the user is logged in
if(isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    
    // Assuming you have established a database connection
    // Query to select premium column value for the logged-in user
    $query = "SELECT premium FROM customer WHERE customer_id = '$customer_id'";
    
    // Execute the query
    $result = mysqli_query($conn, $query);
    
    // Check if query execution was successful
    if($result) {
        // Fetch premium column value
        $row = mysqli_fetch_assoc($result);
        $premium = $row['premium'];
        
        // If premium is "Yes", output JavaScript to hide the div
        if($premium == "Yes") {
            echo "<script>document.addEventListener('DOMContentLoaded', function() { document.querySelector('.blur-overlay').style.display = 'none'; });</script>";
        }
    } else {
        // Handle query execution error
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Handle case where user is not logged in
    echo "";
}

// Check if the user is logged in
if(isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];

$query = "SELECT * FROM customer WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $query);

// Check if the query was successful and data was returned
if ($result && mysqli_num_rows($result) > 0) {
    $portfolio_row = mysqli_fetch_assoc($result);
} else {
    // No portfolio data found
    $portfolio_row = [];
}
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNBOXED</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
    <div class="nav-logo">
    <a class="navbar-brand" href="Home.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
    <?php if (!empty($customerName)): ?>
        <a class="navbar-brand">|</a>
        <a class="navbar-brand"><?php echo $customerName; ?></a>
    <?php endif; ?>

    <?php if (!empty($name) && $loggedInCustomerID === $customerID): ?>
    <!-- If user is logged in and the customer_id matches, display edit options -->
    <a href="#" class="edit-name">Change</a>
<?php endif; ?>
</div>
        <div class="navbar-nav" id="navbarLinks">
            <?php if (!empty($name)): ?>
                <!-- If user is logged in, display navigation links -->
                <a class="nav-link" href="About.php">ABOUT</a>
                <a class="nav-link" href="FAQ.php">FAQ</a>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($image_dp)): ?>
                            <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="Profile.php?customer_id=<?php echo urlencode($customer_id); ?>&name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Profile</a>
                    <a class="dropdown-item" href="Purchase.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Purchase</a>
                    <a class="dropdown-item" href="Orders.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">Orders</a>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?logout=1">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <!-- If user is not logged in, display Sign Up and Login links -->
                <a class="nav-link" href="#">ABOUT</a>
                <a class="nav-link" href="#">FAQ</a>
                <a class="nav-link" href="Create-Box.php">Sign Up</a>
                <a class="nav-link">|</a>
                <a class="nav-link" href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Modal for updating customer name -->
<div class="modal fade" id="updateNameModal" tabindex="-1" aria-labelledby="updateNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateNameModalLabel">Update Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for updating name -->
                <form id="updateNameForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="mb-name">
                        <label for="newName" class="form-label-name">New Name</label>
                        <input type="text" class="form-control-name" id="newName" name="new_name" required>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn-name-close" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn-name-update">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="profile-con">
    <?php
    // Display the profile background
    echo '<img src="./images/prof-bg.png" alt="" class="profile-bg">';
    
    // Check if the customer ID is provided and it's not the logged-in user's ID
    if (!empty($customerID) && $customerID !== $loggedInCustomerID) {
        // Fetch the customer's image_dp based on the provided customer ID
        $customerImageQuery = "SELECT image_dp FROM customer WHERE customer_id = $customerID";
        $customerImageResult = $conn->query($customerImageQuery);

        if ($customerImageResult->num_rows > 0) {
            $customerImageData = $customerImageResult->fetch_assoc();
            $customerImage = $customerImageData['image_dp'];
            // Display the customer's profile image
            echo '<img src="' . $customerImage . '" alt="' . $customerName . '" class="profile-pic">';
        } else {
            // If no image is found for the customer, display a default image
            echo '<img src="default_customer_image.jpg" alt="' . $customerName . '" class="profile-pic">';
        }
    } else {
        // If the customer ID is not provided or it's the logged-in user's ID, display the logged-in user's image
        if (!empty($image_dp)) {
            // Display the user's profile image
            echo '<img src="' . $image_dp . '" alt="' . $name . '" class="profile-pic">';
        }
    }
   if (!empty($name) && $loggedInCustomerID === $customerID): ?>
        <!-- If user is logged in and the customer_id matches, display edit options -->
        <a href="#" class="edit-pic" id="edit-pic">Change</a>
    <?php endif; ?>
</div>

<div class="modal fade" id="updateImageModal" tabindex="-1" aria-labelledby="updateImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateImageModalLabel">Update Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for updating image -->
                <form id="updateImageForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-image">
                        <label for="image" class="form-label-image">Choose Image:</label>
                        <input type="file" class="form-control-image" id="image" name="image" accept="image/*" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-image-close" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn-image-update">Update Image</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Editing Portfolio -->
<div class="modal fade" id="portfolioModal" tabindex="-1" role="dialog" aria-labelledby="portfolioModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="portfolioModalLabel">Edit Portfolio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for updating portfolio information -->
                <form id="portfolioForm" method="post" action="Profile.php">
                    <!-- Your form fields for updating portfolio information go here -->
                    <!-- Example: -->
                    <div class="form-group">
                        <label for="name" class="form-label-contact">Name</label>
                        <input type="text" class="form-control-name" id="name" name="name" placeholder="Enter name" value="<?php echo isset($portfolio_row['name']) ? $portfolio_row['name'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="field" class="form-label-contact">Field</label>
                        <select name="field" id="field" onchange="showOtherField()" class="form-control-field" required>
                    <option value=""><?php echo isset($portfolio_row['field']) ? $portfolio_row['field'] : ''; ?></option>
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
                </select>
                    </div>
                    <div class="form-group">
                        <label for="skill" class="form-label-contact">Skill</label>
                        <div class="skills-checkboxes">
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Graphic Design"> Graphic Design</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="UI/UX Design"> UI/UX Design</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Copywriting"> Copywriting</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Creative Writing"> Creative Writing</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Web Development"> Web Development</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Software Development"> Software Development</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Project Management"> Project Management</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Financial Management"> Financial Management</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Painting"> Painting</label><br>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Drawing"> Drawing</label><br>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Digital Art"> Digital Art</label>
                        <label class="field-label"><input type="checkbox" name="skills[]" value="Event Photography"> Event Photography</label>
                    </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="email" class="form-label-contact">Email</label>
                        <input type="email" class="form-control-email" id="email" name="email" placeholder="Enter email" value="<?php echo isset($portfolio_row['email']) ? $portfolio_row['email'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label-contact">Address</label>
                        <input type="text" class="form-control-address" id="address" name="address" placeholder="Enter address" value="<?php echo isset($portfolio_row['address']) ? $portfolio_row['address'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="contact_num" class="form-label-contact">Contact Number</label>
                        <input type="text" class="form-control-contact" id="contact_num" name="contact_num" placeholder="Enter contact number" value="<?php echo isset($portfolio_row['contact_num']) ? $portfolio_row['contact_num'] : ''; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-edit-port">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Display "My Portfolio" section regardless of user login state -->
<div class="prof-info">
    <div class="port-label">
        <p>MY PORTFOLIO</p>
        <?php if (!empty($name) && $loggedInCustomerID === $customerID): ?>
        <!-- If user is logged in and the customer_id matches, display edit options -->
        <a href="#" class="edit-portfolio" data-toggle="modal" data-target="#portfolioModal">Edit</a>
    <?php endif; ?>
        <div class="premium">
        <div class="portfolio">
            <?php
            // Check if the customer_id of the profile is provided in the URL parameters
            if (isset($_GET['customer_id'])) {
                $profile_customer_id = $_GET['customer_id'];

                // SQL query to fetch portfolio information based on the provided customer_id
                $portfolio_query = "SELECT name, field, skill, email, address, contact_num FROM customer WHERE customer_id = '$profile_customer_id'";
                $portfolio_result = $conn->query($portfolio_query);

                if ($portfolio_result && $portfolio_result->num_rows > 0) {
                    $portfolio_row = $portfolio_result->fetch_assoc();
                    // Display portfolio information
                    echo "<p>My name is " . $portfolio_row['name'] . "</p>";
                    echo "<p>I am " . $portfolio_row['field'] . "</p>";
                    
                    // Check if 'skill' field contains multiple skills separated by comma
                    if (strpos($portfolio_row['skill'], ',') !== false) {
                        $skills = explode(',', $portfolio_row['skill']);
                        echo "<p>Skills:</p>";
                        echo "<ul>";
                        foreach ($skills as $skill) {
                            echo "<li>" . trim($skill) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>Skill: " . $portfolio_row['skill'] . "</p>";
                    }
                    
                    echo "<p>Email: " . $portfolio_row['email'] . "</p>";
                    echo "<p>Address: " . $portfolio_row['address'] . "</p>";
                    echo "<p>Contact Number: " . $portfolio_row['contact_num'] . "</p>";
                } else {
                    echo "Portfolio information not found for the selected profile.";
                }
                
            } else {
                echo "Profile customer ID not provided.";
            }
            ?>
        </div>
        <div class="blur-overlay">
        <!-- Unlock Button -->
            <button id="unlockBtn" class="premium-btn">Unlock</button>
        </div>
        </div>
    </div>
    <div class="showcase-con-box">
    <p class="lbl">Showcase</p>

    <?php
// Check if the 'from_card' parameter is present in the URL
$fromCard = isset($_GET['from_card']) ? $_GET['from_card'] : false;
?>

<!-- Button trigger modal -->
<?php if (!empty($name) && !$fromCard): ?>
    <button type="button" class="add-showcase" data-bs-toggle="modal" data-bs-target="#addShowcaseModal">
        Add Showcase
    </button>
<?php endif; ?>

<!-- Modal HTML -->
<div id="premiumModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Unlock Premium</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <center>
        <p>Would you like to try premium for free?</p>
        </center>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn-close-add" data-bs-dismiss="modal" aria-label="Close">No</button>
        <button type="button" class="btn-update-add" id="premiumYesBtn">Yes</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Modal for adding showcase -->
<div class="modal fade" id="addShowcaseModal" tabindex="-1" role="dialog" aria-labelledby="addShowcaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShowcaseModalLabel">Add Showcase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for adding showcase goes here -->
                <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-1">
                    <img src="" class="show-pic" alt="">
                        <label for="showcaseImage" class="showcase-label">Showcase Image</label>
                        <input type="file" class="showcase-profile" id="showcaseImage" name="showcase_dp" onchange="displayImage(this)" required>
                    </div>
                    <div class="mb-1">
                        <label for="showcaseName" class="form-label">Showcase Name</label>
                        <input type="text" class="form-control" id="showcaseName" name="showcase_name" placeholder="Showcase Name" required>
                    </div>
                    <div class="mb-1">
                        <label for="customerID" class="form-label">Customer ID</label>
                        <input type="text" class="form-control" id="customerID" name="customer_id" placeholder="Customer ID" required>
                    </div>
                    <div class="mb-1">
                        <label for="customerName" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customerName" name="owner" placeholder="Customer Name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for editing showcase -->
<div class="modal fade" id="editShowcaseModal" tabindex="-1" role="dialog" aria-labelledby="editShowcaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editShowcaseModalLabel">Edit Showcase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="editShowcaseName" class="form-label">Showcase Name</label>
                        <input type="text" class="form-control-showcase" id="editShowcaseName" name="edit_showcase_name" placeholder="Showcase Name" required>
                    </div>
                    <div class="mb-image">
                        <label for="editShowcaseImage" class="form-label-img">Showcase Image</label>
                        <input type="file" class="form-control-showcase-img" id="editShowcaseImage" name="edit_showcase_dp" accept="image/*">
                    </div>
                    <input type="hidden" id="editCustomerID" name="edit_customer_id" value="<?php echo $customerID; ?>">
                    <input type="hidden" id="originalShowcaseName" name="original_showcase_name">
                    <button type="submit" class="btn btn-primary" id="editShowcaseSubmit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="showcase-con-box">
    <?php
    // Fetch showcase data based on the retrieved customer ID
    if (!empty($customerID)) {
        $showcase_query = "SELECT showcase_id, showcase_name, showcase_dp, owner FROM showcase WHERE customer_id = $customerID";
        $showcase_result = $conn->query($showcase_query);

        if ($showcase_result->num_rows > 0) {
            // Display the showcase data
            while ($showcase_row = $showcase_result->fetch_assoc()) {
                echo '<div class="showcase-box style="width: 18rem;">';
                // Check if the logged-in user is the owner of the showcase
                if ($loggedInCustomerID === $customerID) {
                    // Display the dropdown menu with options for Edit and Delete
                    echo '<div class="dropdown">';
                    echo '<button class="bi bi-three-dots-vertical" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false"></button>';
                    echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    echo '<li><button class="dropdown-item" onclick="editShowcase(\'' . $showcase_row["showcase_name"] . '\')">Edit</button></li>';
                    echo '<li>';
                    echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to delete this showcase?\');">';
                    echo '<button type="submit" name="delete_showcase" class="dropdown-item">Delete</button>';
                    echo '<input type="hidden" name="customer_id" value="' . $customerID . '">';
                    echo '<input type="hidden" name="showcase_name" value="' . htmlspecialchars($showcase_row['showcase_name'] ?? '') . '">';
                    echo '</form>';
                    echo '</li>';
                    echo '</ul>';
                    echo '</div>';

                }
                echo '<a href="Showcase-items.php?showcase_id=' . urlencode($showcase_row['showcase_id']) . '&showcase_name=' . urlencode($showcase_row['showcase_name']) . '&showcase_dp=' . urlencode($showcase_row['showcase_dp']) . '&customer_id=' . $customerID . '&owner=' . $showcase_row['owner'] . '">';
                echo '<img class="showcase-box-img" src="' . $showcase_row["showcase_dp"] . '" alt="' . $showcase_row["showcase_name"] . '">';
                echo '<div class="showcase-item">';
                echo '<h6 class="showcase-box-name">' . $showcase_row["showcase_name"] . '</h6>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo "No showcase items found for this customer.";
        }
    } else {
        echo "Customer ID not provided.";
    }
    ?>
</div>

</body>
<script>
$(document).ready(function() {
  // Add event listener to the "Yes" button
  $("#premiumYesBtn").click(function() {
    // Make an AJAX request to the PHP script
    $.ajax({
      url: 'Profile.php', // PHP script to update premium column
      type: 'POST',
      data: { premium: 'Yes' }, // Data to send to the server
      success: function(response) {
        // Handle the response from the server
        console.log(response);
      },
      error: function(xhr, status, error) {
        // Handle errors
        console.error(xhr.responseText);
      }
    });
  });
});

$(document).ready(function(){
    // Add click event listener to the unlock button
    $('#unlockBtn').click(function(){
        // Check if the user is logged in
        <?php
        // This is a simplified example of checking if the user is logged in using PHP session
        // You should replace it with your actual authentication logic
        if (isset($_SESSION['customer_id'])) {
            // If the user is logged in, show the premium modal
            echo "$('#premiumModal').modal('show');";
        } else {
            // If the user is not logged in, redirect to the login page
            echo "window.location.href = 'login.php';";
        }
        ?>
    });
});

 function editShowcase(showcaseName) {
        // Set the current showcase name in the modal form
        document.getElementById('editShowcaseName').value = showcaseName;
        document.getElementById('originalShowcaseName').value = showcaseName;

        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById('editShowcaseModal'));
        modal.show();
    }

// JavaScript function to show confirmation dialog before deleting showcase
function confirmDelete() {
    return confirm("Are you sure you want to delete this showcase?");
}

document.addEventListener("DOMContentLoaded", function() {
    // JavaScript to show the modal for updating name
    document.querySelector('.edit-name').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default link behavior
        // Get the modal
        var modal = new bootstrap.Modal(document.getElementById('updateNameModal'));
        // Show the modal
        modal.show();
    });
});

   document.addEventListener("DOMContentLoaded", function() {
        // JavaScript to show the modal for updating image
        document.getElementById('edit-pic').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default link behavior
            // Get the modal
            var modal = new bootstrap.Modal(document.getElementById('updateImageModal'));
            // Show the modal
            modal.show();
        });
    });

     // JavaScript function to redirect to Showcase-items.php with showcase_name parameter
     function redirectToShowcaseItems(showcaseName) {
        window.location.href = 'Showcase-items.php?showcase_name=' + encodeURIComponent(showcaseName);
    }
    // JavaScript function to handle button click and show modal
    document.getElementById('addShowcaseButton').addEventListener('click', function() {
        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById('addShowcaseModal'));
        modal.show();
    });

    
    function displayImage(input) {
    var file = input.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = input.parentElement.querySelector('.show-pic');
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
     // JavaScript to handle hover effect
     const showcaseBoxes = document.querySelectorAll('.showcase-box');
    showcaseBoxes.forEach(box => {
        box.addEventListener('mouseover', () => {
            box.querySelector('.bi-three-dots-vertical').style.display = 'block';
        });
        box.addEventListener('mouseout', () => {
            box.querySelector('.bi-three-dots-vertical').style.display = 'none';
        });
    });
</script>
</html>
<?php
// Close the database connection
$conn->close();
?>
