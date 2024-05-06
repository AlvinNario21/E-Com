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
    $stmt = $conn->prepare($updateNameQuery);
    
    if (!$stmt) {
        // Handle preparation error
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("si", $newName, $loggedInCustomerID);

    if (!$stmt) {
        // Handle binding error
        die("Error binding parameters: " . $stmt->error);
    }

    // Execute the statement
    if ($stmt->execute()) {
        // Name updated successfully
        $_SESSION['name'] = $newName; // Update session name
        echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
    } else {
        // Error updating name
        echo '<script>alert("Error updating name: ' . $stmt->error . '");</script>';
    }

    // Close statement
    $stmt->close();
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
                <a class="nav-link" href="#">ABOUT</a>
                <a class="nav-link" href="#">FAQ</a>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($image_dp)): ?>
                            <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="Profile.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">Profile</a></li>
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

<!-- Display "My Portfolio" section regardless of user login state -->
<div class="prof-info">
    <div class="port-label">
        <p>My Portfolio</p>
        <div class="portfolio">
            <!-- Include portfolio content here -->
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
                        <input type="text" class="form-control" id="customerName" name="customer_name" placeholder="Customer Name" required>
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
                <!-- Form for editing showcase goes here -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="editShowcaseName" class="form-label">Showcase Name</label>
                        <input type="text" class="form-control" id="editShowcaseName" name="edit_showcase_name" placeholder="Showcase Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editShowcaseImage" class="form-label">Showcase Image</label>
                        <input type="file" class="form-control" id="editShowcaseImage" name="edit_showcase_dp" accept="image/*">
                    </div>
                    <input type="hidden" id="editCustomerID" name="edit_customer_id" value="<?php echo $customerID; ?>">
                    <input type="hidden" id="originalShowcaseName" name="original_showcase_name">
                    <button type="submit" class="btn btn-primary" name="save_changes">Save Changes</button>
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
                    echo '<button class="bi bi-three-dots-vertical" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">';
                    echo '</button>';
                    echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    echo '<li><button class="dropdown-item" onclick="editShowcase(\'' . $showcase_row["showcase_name"] . '\')">Edit</button></li>';
                    echo '<li><form method="post"><button type="submit" name="delete_showcase" class="dropdown-item">Delete</button>';
                    echo '<input type="hidden" name="customer_id" value="' . $customerID . '">';
                    echo '<input type="hidden" name="showcase_name" value="' . htmlspecialchars($showcase_row['showcase_name'] ?? '') . '">';
                    echo '</form></li>';
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

function editShowcase(showcaseName) {
        // Set the current showcase name in the modal form
        document.getElementById('editShowcaseName').value = showcaseName;

        // Show the modal
        var modal = new bootstrap.Modal(document.getElementById('editShowcaseModal'));
        modal.show();
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
