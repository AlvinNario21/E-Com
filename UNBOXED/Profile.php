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
        echo "<script>window.location.href = 'Profile.php?customer_id=$customerID&name=$name&image_dp=$image_dp';</script>";
        exit();
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
                echo "<script>window.location.href = 'Profile.php?customer_id=$customerID&name=$name&image_dp=$image_dp';</script>";
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
    echo "<script>window.location.href = 'Profile.php?customer_id=$customerID&name=$name&image_dp=$image_dp';</script>";
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
    echo "<script>window.location.href = 'Profile.php?name=$name&image_dp=$image_dp&customer_id=$customerID'</script>";

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
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for updating name -->
                <form id="updateNameForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="mb-name">
                        <label for="newName" class="form-label-name">New Name</label>
                        <input type="text" class="form-control-name" id="newName" name="new_name" required>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn-name-close" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
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

<!-- Modal -->
<div id="updateFieldModal" class="modal fade">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Field</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="updateFieldForm" action="update_field.php" method="POST">
          <div class="form-group">
            <label for="field" class="form-label-name">Choose a Field:</label>
            <select name="field" id="field" class="form-control-name" required>
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
          </div>
          <div class="form-group" id="otherFieldContainer" style="display: none;">
            <label for="otherField">Other Field:</label>
            <input type="text" id="otherField" class="form-control" placeholder="Enter other field">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-close-add" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn-update-add">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Skills</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="skill-search" style="color: black;" class="form-control-name mb-3" placeholder="Search skills">
        <p id="skill-list" class="list-group"></p>
        <div id="selected-skills" class="mt-3">
          <p>Selected Skills:</p>
          <p id="selected-skill-list" class="list-group"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-close-add" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-update-add" id="save-selected-skills">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Update Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form to update email -->
                <form id="emailForm">
                    <div class="mb-3">
                        <label for="newEmail" class="form-label-name">New Email Address:</label>
                        <input type="email" class="form-control-name" id="newEmail" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Email</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="updateContactModal" tabindex="-1" role="dialog" aria-labelledby="updateContactModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateContactModalLabel">Update Contact Number</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Form to update contact number -->
        <form id="updateContactForm">
          <div class="form-group">
            <label for="contactNumber" class="form-label-name">New Contact Number:</label>
            <input type="text" class="form-control-name" id="contactNumber" name="contactNumber">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-close-add" data-dismiss="modal">Close</button>
        <button type="button" class="btn-update-add" id="saveContactBtn">Save changes</button>
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
                    echo '<div class="edit-portfolio-name-con">';
                    echo "<p>My name is " . $portfolio_row['name'] . "</p>";
                    if (!empty($name) && $loggedInCustomerID === $customerID){
                        echo '<a href="#" class="edit-name edit-portfolio-name"><i class="bi bi-pencil-square"></i></a>';
                    }
                    echo '</div>';
                    echo '<div class="edit-portfolio-field-con">';
                    echo "<p>I am " . $portfolio_row['field'] . "</p>";
                    if (!empty($name) && $loggedInCustomerID === $customerID){
                        echo '<a href="#" id="editFieldBtn" class="edit-portfolio-field"><i class="bi bi-pencil-square"></i></a>';
                    }
                    echo '</div>';
                    // Check if 'skill' field contains multiple skills separated by comma
                    if (strpos($portfolio_row['skill'], ',') !== false) {
                        $skills = explode(',', $portfolio_row['skill']);
                        echo '<div class="edit-portfolio-field-con">';
                        echo "<p>Skills</p>";
                        if (!empty($name) && $loggedInCustomerID === $customerID){
                            echo '<a href="#" class="add-portfolio-skill"><i class="bi bi-pencil-square"></i></a>';
                        }
                        echo '</div>';
                        echo "<ul>";
                        foreach ($skills as $skill) {
                            echo "<li>" . trim($skill) . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo '<div class="edit-portfolio-field-con">';
                        echo "<p>Skills" . $portfolio_row['skill'] . "</p>";
                        if (!empty($name) && $loggedInCustomerID === $customerID){
                            echo '<a href="#" class="add-portfolio-skill"><i class="bi bi-pencil-square"></i></a>';
                        }
                        echo '</div>';
                    }
                    echo '<div class="edit-portfolio-field-con">';
                    echo "<p>Email: " . $portfolio_row['email'] . "</p>";
                    if (!empty($name) && $loggedInCustomerID === $customerID){
                        echo '<a href="#" class="edit-portfolio-email" data-bs-toggle="modal" data-bs-target="#emailModal">
                        <i class="bi bi-pencil-square"></i>
                    </a>';
                    }
                    echo '</div>';
                    echo '<div class="edit-portfolio-field-con">';
                    echo "<p>Address: " . $portfolio_row['address'] . "</p>";
                    if (!empty($name) && $loggedInCustomerID === $customerID){
                        echo '<a href="#" class="edit-portfolio-address"><i class="bi bi-pencil-square"></i></a>';
                    }
                    echo '</div>';
                    echo '<div class="edit-portfolio-field-con">';
                    echo "<p>Contact Number: " . $portfolio_row['contact_num'] . "</p>";
                    if (!empty($name) && $loggedInCustomerID === $customerID){
                        echo '<a href="#" class="edit-portfolio-contact" data-toggle="modal" data-target="#updateContactModal">
                        <i class="bi bi-pencil-square"></i>
                      </a>';
                    }
                    echo '</div>';
                } else {
                    echo "Portfolio information not found for the selected profile.";
                }
                
            } else {
                echo "Profile customer ID not provided.";
            }
            ?>
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
    <<div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editShowcaseModalLabel">Edit Showcase</h5>
                <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
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

<!-- Bootstrap modal markup -->
<div class="modal fade" id="updateAddressModal" tabindex="-1" role="dialog" aria-labelledby="updateAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateAddressModalLabel">Update Address</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
        <!-- Form to update the address -->
        <form id="updateAddressForm">
          <div class="form-group">
            <label for="newAddress" class="form-label-name">New Address:</label>
            <input type="text" class="form-control-name" id="newAddress" placeholder="Enter new address">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-close-add" data-dismiss="modal">Close</button>
        <button type="submit" class="btn-update-add" id="updateAddressBtn">Update</button>
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
document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.querySelector('.edit-portfolio-contact');
    const saveButton = document.querySelector('#saveContactBtn');
    const contactNumberInput = document.querySelector('#contactNumber');

    // Add event listener to the edit button
    editButton.addEventListener('click', function () {
      // Get customer ID from data attribute
      const customerId = this.dataset.customerId;
      // Here you can fetch the current contact number and populate the input field if needed
      
      // For example, fetch contact number via AJAX
      fetchContactNumber(customerId);
    });

    // Function to fetch contact number via AJAX
    function fetchContactNumber(customerId) {
      // Implement AJAX logic to fetch contact number based on customer ID
      // Example AJAX request using fetch API
      fetch('fetch_contact.php?customerId=' + customerId)
        .then(response => response.json())
        .then(data => {
          // Populate the input field with the fetched contact number
          contactNumberInput.value = data.contactNumber;
        })
        .catch(error => console.error('Error fetching contact number:', error));
    }

    // Add event listener to the save button
    saveButton.addEventListener('click', function () {
      // Retrieve the updated contact number from the input field
      const updatedContactNumber = contactNumberInput.value;
      // Get customer ID from data attribute
      const customerId = editButton.dataset.customerId;

      // Send AJAX request to update contact number
      updateContactNumber(customerId, updatedContactNumber);
    });

    // Function to send AJAX request to update contact number
    function updateContactNumber(customerId, updatedContactNumber) {
      // Implement AJAX logic to update contact number based on customer ID
      // Example AJAX request using fetch API
      fetch('update_contact.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          customerId: customerId,
          updatedContactNumber: updatedContactNumber
        })
      })
      .then(response => response.json())
      .then(data => {
        // Handle response if needed
        console.log('Contact number updated successfully:', data);
        // Close the modal
        $('#updateContactModal').modal('hide');
      })
      .catch(error => console.error('Error updating contact number:', error));
    }
  });

$(document).ready(function() {
    // When the edit button is clicked, show the modal
    $('.edit-portfolio-address').click(function(e) {
      e.preventDefault();
      $('#updateAddressModal').modal('show');
    });

    // When the update address button inside the modal is clicked
    $('#updateAddressBtn').click(function() {
      // Get the new address value from the input field
      var newAddress = $('#newAddress').val();

      // Send an AJAX request to update the address
      $.ajax({
        url: 'update_address.php',
        type: 'POST',
        data: {
          newAddress: newAddress
        },
        success: function(response) {
          // Handle the response from the server
          console.log(response);
          if (response === 'success') {
            // Reload the page upon successful address update
            location.reload();
          } else {
            // Close the modal if the update was not successful
            $('#updateAddressModal').modal('hide');
          }
        },
        error: function(xhr, status, error) {
          // Handle errors
          console.error(error);
        }
      });
    });
  });

$(document).ready(function() {
    // When the edit button is clicked, show the modal
    $('.edit-portfolio-address').click(function(e) {
      e.preventDefault();
      $('#updateAddressModal').modal('show');
    });

    // When the update address button inside the modal is clicked
    $('#updateAddressBtn').click(function() {
      // Get the new address value from the input field
      var newAddress = $('#newAddress').val();

      // Here you can send an AJAX request to update the address on the server
      // For demonstration purpose, let's just log the new address to the console
      console.log('New address:', newAddress);

      // Close the modal
      $('#updateAddressModal').modal('hide');
    });
  });

document.getElementById('emailForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission
    var customerId = <?php echo isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 'null'; ?>; // Assuming customer_id is stored in session
    var newEmail = document.getElementById('newEmail').value;
    
    // AJAX request to update email
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_email.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            if (xhr.status == 200) {
                console.log(xhr.responseText); // Log server response
                // Parse the response from server
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Show success message
                    alert('Email updated successfully!');
                    // Reload the page
                    location.reload();
                } else {
                    console.error('Error:', response.error);
                    // Handle error appropriately
                }
            } else {
                console.error('Error:', xhr.status, xhr.statusText);
                // Handle error appropriately
            }
        }
    };
    xhr.send('newEmail=' + encodeURIComponent(newEmail));
    
    // Close the modal
    var emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
    emailModal.hide();
});

$(document).ready(function(){
    $(".add-portfolio-skill").click(function(){
        // Show the modal using Bootstrap's modal method
        $("#myModal").modal('show');
    });

    // Search skills when typing in the input field
    $("#skill-search").keyup(function(){
        var searchTerm = $(this).val();
        $.ajax({
            url: "fetch_skills.php",
            method: "POST",
            data: {term: searchTerm},
            success: function(response){
                $("#skill-list").html(response);
            }
        });
    });

    // Add selected skills to the list
    $(document).on("click", "#skill-list li", function(){
        var skillName = $(this).text();
        $("#selected-skill-list").append("<li class='list-group-item skill'>" + skillName + "</li>"); // Ensure 'skill' class is added
    });

    // Save selected skills
$("#save-selected-skills").click(function() {
    var selectedSkills = []; // Array to store selected skills

    // Loop through each selected skill and add it to the array
    $("#selected-skill-list .skill").each(function() {
        selectedSkills.push($(this).text());
    });

    // Check if any skills are selected
    if (selectedSkills.length > 0) {
        // Send selected skills to PHP script using AJAX
        $.ajax({
            type: "POST",
            url: "insert_skill.php",
            data: {
                selectedSkills: selectedSkills
            },
            success: function(response) {
                // Check if insertion was successful
                if (response.trim() === "success") {
                    // Show success alert
                    alert("Skills inserted successfully");
                    // Reload the page
                    location.reload();
                } else {
                    // Show error alert if insertion failed
                    alert("Error inserting skills: " + response);
                }
            },
            error: function(xhr, status, error) {
                // Handle error if needed
                console.error(xhr.responseText);
            }
        });
    } else {
        console.log("No skills selected");
    }
});
});

// Function to show/hide other field input based on selection
function showOtherField() {
  var field = document.getElementById("field").value;
  var otherFieldInput = document.getElementById("otherField");
  if (field === "other") {
    otherFieldInput.style.display = "block";
  } else {
    otherFieldInput.style.display = "none";
  }
}

// Submit form function - handle form submission as per your requirement
document.getElementById("updateFieldForm").addEventListener("submit", function(event) {
  event.preventDefault();
  // Handle form submission here, e.g., send data to server via AJAX
  // Then close the modal
  modal.style.display = "none";
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
