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
}// Start session to manage user login state

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
    // User is logged in, fetch the user's customer_id from the session
    $customer_id = isset($_SESSION["customer_id"]) ? $_SESSION["customer_id"] : '';

    // Fetch count of items in the cart for the logged-in customer
    $cart_item_count = 0;
    if (!empty($customer_id)) {
        $count_query = "SELECT COUNT(*) AS total_items FROM cart_items WHERE customer_id = '$customer_id'";
        $count_result = $conn->query($count_query);
        if ($count_result && $count_result->num_rows > 0) {
            $count_row = $count_result->fetch_assoc();
            $cart_item_count = $count_row['total_items'];
        }
    }
} else {
    // User is not logged in, set default count to 0
    $cart_item_count = 0;
}

// Fetch name and image_dp from the customer table
$sql = "SELECT customer_id, name, image_dp FROM customer";
$result = $conn->query($sql);


// Check if the user is logged in
if (isset($_SESSION["email"])) {
    // User is logged in, fetch the user's name and image_dp from the session
    $name = $_SESSION["name"]; // Assuming 'name' is the column name for user's name
    $image_dp = $_SESSION["image_dp"]; // Assuming 'image_dp' is the column name for user's image
} else {
    // User is not logged in, set default values
    $name = "";
    $image_dp = "";
}
// Check if the session variable is set and true (meaning "Open the Boxes" button is clicked)
$displayUserInfo = !(isset($_SESSION['open_boxes']) && $_SESSION['open_boxes']);

// Retrieve the showcase_name from the URL parameter
if (isset($_GET['showcase_name'])) {
    $showcase_name = $_GET['showcase_name'];
} else {
    $showcase_name = ""; // Default value if showcase_name is not provided
}

// Fetch the showcase_dp based on the provided showcase_name
if (!empty($showcase_name)) {
    $showcase_dp_query = "SELECT showcase_dp FROM showcase WHERE showcase_name = '$showcase_name'";
    $showcase_dp_result = $conn->query($showcase_dp_query);

    if ($showcase_dp_result && $showcase_dp_result->num_rows > 0) {
        $showcase_dp_row = $showcase_dp_result->fetch_assoc();
        $showcase_dp = $showcase_dp_row['showcase_dp'];
    } else {
        $showcase_dp = ""; // Default value if showcase_dp is not found
    }
} else {
    $showcase_dp = ""; // Default value if showcase_name is empty
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract form data
    $customer_id = $_POST["customer_id"];
    $owner = $_POST["owner"];
    $showcase_id = $_POST["showcase_id"];
    $showcase_name = $_POST["showcase_name"];
    $product_img = $_FILES["product_img"]["name"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $stocks = $_POST["stocks"];
    $description = $_POST["description"];

    // Directory where you want to store uploaded images
    $upload_directory = "./images/";

    // Handle image upload and move it to the upload directory
    $target_file = $upload_directory . basename($_FILES["product_img"]["name"]);
    move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file);

    // Insert data into the product table with directory path prepended to product_img
    $insert_query = "INSERT INTO product (customer_id, owner, showcase_id, showcase_name, product_img, name, price, stocks, description) VALUES ('$customer_id', '$owner', '$showcase_id', '$showcase_name', '$target_file', '$name', '$price', '$stocks', '$description')";

    if ($conn->query($insert_query) === TRUE) {
        // Insertion successful
        echo "Product added successfully.";
        header("location: Profile.php");
        exit;
    } else {
        // Error handling
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract form data
    $productId = $_POST["product_id"];
    $productName = $_POST["name"];
    $productPrice = $_POST["price"];
    // Add more fields here if needed

    // Construct the SQL UPDATE query for product table
    $productSql = "UPDATE product SET name='$productName', price='$productPrice'";

    // Check if product_img is provided and handle image update
    if (!empty($_FILES["product_img"]["name"])) {
        // Handle image upload and move it to the upload directory
        $target_directory = "./images/";
        $target_file = $target_directory . basename($_FILES["product_img"]["name"]);
        move_uploaded_file($_FILES["product_img"]["tmp_name"], $target_file);
        
        // Append product_img field to the SQL query
        $productImg = $target_file;
        $productSql .= ", product_img='$productImg'";
    }

    // Complete the SQL query for product table with WHERE clause
    $productSql .= " WHERE product_id='$productId'";

    // Execute the SQL query for product table
    if ($conn->query($productSql) === TRUE) {
        // Product updated successfully

        // Construct the SQL UPDATE query for order_item table
        $orderItemSql = "UPDATE order_item SET product_name='$productName', price='$productPrice'";
        
        // Check if product_img is provided and append to the SQL query for order_item table
        if (!empty($productImg)) {
            $orderItemSql .= ", product_img='$productImg'";
        }

        // Complete the SQL query for order_item table with WHERE clause
        $orderItemSql .= " WHERE product_id='$productId'";

        // Execute the SQL query for order_item table
        if ($conn->query($orderItemSql) === TRUE) {
            // Order item updated successfully
            echo "<script>window.location.href = '{$_SERVER['PHP_SELF']}';</script>";
        } else {
            // Error handling for order_item table
            echo "Error updating order item: " . $conn->error;
        }
    } else {
        // Error handling for product table
        echo "Error updating product: " . $conn->error;
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
    <?php if (!empty($showcase_name)): ?>
        <a class="navbar-brand">|</a>
        <a class="navbar-brand"><?php echo $showcase_name; ?></a> <!-- Display showcase_name here -->
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
                        <?php else: ?>
                            <?php echo $name; ?>
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
                <a class="nav-link" href="Create-Box.php">Sign Up</a>
                <a class="nav-link">|</a>
                <a class="nav-link" href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Modal for adding product -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <!-- Add hidden input fields to capture customer_id, owner, showcase_id, and showcase_name -->
                    <input type="hidden" name="customer_id" value="<?php echo isset($_SESSION["customer_id"]) ? $_SESSION["customer_id"] : ''; ?>">
                    <input type="hidden" name="owner" value="<?php echo isset($_SESSION["name"]) ? $_SESSION["name"] : ''; ?>">
                    <input type="hidden" name="showcase_id" value="<?php echo isset($_GET["showcase_id"]) ? $_GET["showcase_id"] : ''; ?>">
                    <input type="hidden" name="showcase_name" value="<?php echo isset($_GET["showcase_name"]) ? $_GET["showcase_name"] : ''; ?>">
                    <div class="mb-image">
                        <label for="productImage" class="form-label-image">Product Image</label>
                        <input type="file" class="form-control-image" id="productImage" name="product_img" required>
                    </div>
                    <div class="mb-1">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="name" placeholder="Product Name" required>
                    </div>
                    <div class="mb-1">
                        <label for="productPrice" class="form-label">Product Price</label>
                        <input type="number" step="0.01" class="form-control-price" id="productPrice" name="price" placeholder="Product Price" required>
                    </div>
                    <div class="mb-1">
                        <label for="productStocks" class="form-label">Stocks</label>
                        <input type="number" class="form-control-stocks" id="productStocks" name="stocks" placeholder="Stocks" required>
                    </div>
                    <div class="mb-1">
                        <label for="productDescription" class="form-label">Product Description</label>
                        <textarea class="form-control-description" id="productDescription" name="description" placeholder="Product Description" required></textarea>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn-close-add" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn-update-add" name="submit" id="submitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="product-profile">
    <?php if (!empty($showcase_dp) || empty($showcase_name)): ?>
        <!-- Display profile background and picture if available or in guest mode -->
        <img src="./images/prof-bg.png" alt="" class="profile-bg">
        <?php if (!empty($showcase_dp)): ?>
            <!-- Display user profile picture if available -->
            <img src="<?php echo $showcase_dp; ?>" alt="<?php echo $showcase_name; ?>" class="showcase-pic">
        <?php endif; ?>
    <?php else: ?>
        <!-- Display default background if no profile picture available -->
        <img src="./images/prof-bg.png" alt="" class="profile-bg">
    <?php endif; ?>
</div>

<?php

// Display the "Add Product" button only if the user is logged in and owns the showcased products
if (!empty($name) && isset($_GET['showcase_id']) && isset($_GET['showcase_name'])) {
    $showcase_id = $_GET['showcase_id'];
    $showcase_name = $_GET['showcase_name'];

    // Check if the logged-in user is the owner of the showcased products
    $check_owner_query = "SELECT COUNT(*) AS num_products FROM product WHERE owner = '$name' AND showcase_id = '$showcase_id'";
    $check_owner_result = $conn->query($check_owner_query);
    
    if ($check_owner_result && $check_owner_result->num_rows > 0) {
        $check_owner_row = $check_owner_result->fetch_assoc();
        $num_products = $check_owner_row['num_products'];

        // Display the button if the logged-in user is the owner of the showcased products
        if ($num_products > 0) {
            echo '<button type="button" class="add-product" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>';
        }
    }
}
?>

<div class="product-card-container">
    <?php
// Check if the showcase_id and showcase_name are set in the URL parameters
if (isset($_GET['showcase_id']) && isset($_GET['showcase_name'])) {
    $showcase_id = $_GET['showcase_id'];
    $showcase_name = $_GET['showcase_name'];

    // Fetch product data matching the showcase_id and showcase_name
    $sql = "SELECT p.product_id, p.product_img, p.name, p.price, p.description, p.sold
            FROM product p 
            WHERE p.showcase_id = '$showcase_id' AND p.showcase_name = '$showcase_name'";
} else if(isset($_GET['showcase_name']) && !empty($_GET['showcase_name']) && isset($_GET['showcase_dp']) && !empty($_GET['showcase_dp'])) {
    $showcase_name = $_GET['showcase_name']; // Retrieve showcase_name from URL parameter

    // Fetch product data matching showcase_name
    $sql = "SELECT s.showcase_id, s.showcase_name, p.product_id, p.product_img, p.name, p.price, p.description, p.sold
            FROM showcase s 
            INNER JOIN product p ON s.showcase_name = p.showcase_name 
            WHERE s.showcase_name = '$showcase_name'";
} else {
    // Handle if neither showcase_id nor showcase_name are provided
    echo "No showcase ID or name provided.";
}
        $result = $conn->query($sql);

        // Display product items with data if result is not empty
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                echo '    <div class="dropdown">';
                if (isset($_SESSION['customer_id'])) {
                    echo '<button class="bi bi-three-dots" type="button" id="dropdownMenuButton_' . $row['product_id'] . '" data-bs-toggle="dropdown" aria-expanded="false">';
                    echo '</button>';
                    echo '        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton_' . $row['product_id'] . '">';
                    echo '            <li><a class="dropdown-item edit-product" data-product-id="' . $row['product_id'] . '" data-product-name="' . urlencode($row['name']) . '" data-product-img="' . urlencode($row['product_img']) . '" data-product-price="' . urlencode($row['price']) . '" href="#">Edit</a></li>';
                    echo '            <li><a class="dropdown-item delete-product" data-product-id="' . $row['product_id'] . '" href="#">Delete</a></li>';
                    echo '        </ul>';
                }
                echo '    </div>';
                echo '        <a href="Product.php?product_id=' . $row['product_id'] . '&product_img=' . urlencode($row['product_img']) . '&product_name=' . urlencode($row['name']) . '&product_price=' . urlencode($row['price']) . '&product_description=' . urlencode($row['description']) . '">';
                echo '            <img src="' . $row['product_img'] . '" class="product-card-img-top" alt="' . $row['name'] . '">';
                echo '            <div class="product-card-body">';
                echo '                <h5 class="product-card-title">' . $row['name'] . '</h5>';
                echo '                <div class="product-card-body-text">';
                echo '                    <p class="product-card-text">₱' . $row['price'] . '</p>';
                echo '                    <p class="product-card-text">' . $row['sold'] . ' sold</p>'; // Display sold data
                echo '                </div>';
                echo '            </div>';
                echo '        </a>';
                echo '    </div>';                                                                                                    
            }
        } else {
            echo "No product items found for this showcase.";
        }
    ?>
</div>

<!-- Modal for editing product -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="product_id" id="editProductId">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editProductName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label">Product Price</label>
                        <input type="number" step="0.01" class="form-control" id="editProductPrice" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductImage" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="editProductImage" name="product_img">
                    </div>
                    <!-- Add other input fields for editing -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-product');

        editButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const productName = decodeURIComponent(this.getAttribute('data-product-name'));
                const productPrice = decodeURIComponent(this.getAttribute('data-product-price'));
                const productImg = decodeURIComponent(this.getAttribute('data-product-img'));

                // Populate modal fields with product details
                document.getElementById('editProductId').value = productId;
                document.getElementById('editProductName').value = productName;
                document.getElementById('editProductPrice').value = productPrice;

                // Show the modal
                const editProductModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                editProductModal.show();
            });
        });
    });
</script>
</html>

<?php
// Close the database connection
$conn->close();
?>