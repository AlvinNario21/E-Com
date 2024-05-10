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

// Fetch the customer ID based on the provided name (if available) or use the logged-in user's customer ID
if (isset($_GET['customer_id'])) {
    $customerID = $_GET['customer_id'];
} else {
    $customerID = $loggedInCustomerID;
}

// Check if the session variable is set and true (meaning "Open the Boxes" button is clicked)
$displayUserInfo = !(isset($_SESSION['open_boxes']) && $_SESSION['open_boxes']);

// Check if the showcase_id and showcase_name are set in the URL parameters
if (isset($_GET['showcase_id']) && isset($_GET['showcase_name'])) {
    // Fetch product data matching the showcase_id and showcase_name
    $showcase_id = $_GET['showcase_id'];
    $showcase_name = $_GET['showcase_name'];
} else {
    // Handle if neither showcase_id nor showcase_name are provided
    echo "No showcase ID or name provided.";
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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $customer_id = isset($_POST["customer_id"]) ? $_POST["customer_id"] : '';
    $owner = isset($_POST["owner"]) ? $_POST["owner"] : '';
    $showcase_id = isset($_POST["showcase_id"]) ? $_POST["showcase_id"] : '';
    $showcase_name = isset($_POST["showcase_name"]) ? $_POST["showcase_name"] : '';
    $product_img = isset($_FILES["product_img"]["name"]) ? $_FILES["product_img"]["name"] : '';
    $product_name = isset($_POST["name"]) ? $_POST["name"] : '';
    $price = isset($_POST["price"]) ? $_POST["price"] : '';
    $stocks = isset($_POST["stocks"]) ? $_POST["stocks"] : '';
    $description = isset($_POST["description"]) ? $_POST["description"] : '';

    // Check if the customer_id exists in the customer table
    $check_customer_query = "SELECT * FROM customer WHERE customer_id = '$customer_id'";
    $check_customer_result = $conn->query($check_customer_query);

    if ($check_customer_result && $check_customer_result->num_rows > 0) {
        // Customer exists, proceed with inserting into the product table

        // File upload directory
        $targetDir = "./images/";

        // File upload path
        $targetFilePath = $targetDir . basename($product_img);

        // Move uploaded file to the destination directory
        if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $targetFilePath)) {
            // Insert data into product table
            $sql = "INSERT INTO product (customer_id, owner, showcase_id, showcase_name, product_img, name, price, stocks, description) 
                    VALUES ('$customer_id', '$owner', '$showcase_id', '$showcase_name', '$targetFilePath', '$product_name', '$price', '$stocks', '$description')";
            
            if (mysqli_query($conn, $sql)) {
                // Redirect to success page or do any further processing
                header("Location: Showcase-items.php?showcase_id=$showcase_id&showcase_name=$showcase_name&showcase_dp=$showcase_dp&customer_id=$customer_id&owner=$name");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } 
}

// Check if the product ID is provided
if(isset($_GET['product_id'])) {
 // Get the product ID
 $product_id = $_GET['product_id'];

 // Construct SQL DELETE query
 $sql = "DELETE FROM product WHERE product_id = '$product_id'";

 // Execute the query
 if ($conn->query($sql) === TRUE) {
     header("Location: {$_SERVER['HTTP_REFERER']}");
     exit();
 } else {
     echo "Error deleting record: " . $conn->error;
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
                <a class="nav-link" href="About.php">ABOUT</a>
                <a class="nav-link" href="FAQ.php">FAQ</a>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($image_dp)): ?>
                            <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                        <?php else: ?>
                            <?php echo $name; ?>
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
            <form method="post" enctype="multipart/form-data" action="Showcase-items.php">
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

// Check if the user is logged in
if (!empty($name) && isset($_GET['showcase_id']) && isset($_GET['showcase_name'])) {
    $showcase_id = $_GET['showcase_id'];
    $showcase_name = $_GET['showcase_name'];

    // Check if the logged-in user is the owner of the showcased products
    $check_owner_query = "SELECT COUNT(*) AS num_products FROM product WHERE owner = '$name' AND showcase_id = '$showcase_id'";
    $check_owner_result = $conn->query($check_owner_query);
    
    if ($check_owner_result && $check_owner_result->num_rows > 0) {
        $check_owner_row = $check_owner_result->fetch_assoc();
        $num_products = $check_owner_row['num_products'];            
    }
    // Check if the parameter indicating navigation from Home.php is present
if (isset($_GET['from_home']) && $_GET['from_home'] === 'true') {
    // Do not display the "Add Product" button
} else {
    // Display the "Add Product" button
    echo '<button type="button" class="add-product" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>';
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
} else {
    // If neither showcase_id nor showcase_name is provided, fetch all product data
    $sql = "SELECT p.product_id, p.product_img, p.name, p.price, p.description, p.sold
            FROM product p";
}
$result = $conn->query($sql);

// Display product items with data if result is not empty
if ($result && $result->num_rows > 0) {
    // Iterate through the results and display product cards
    while ($row = $result->fetch_assoc()) {
                echo '<div class="product-card">';
                if ($loggedInCustomerID === $customerID) {
                echo '    <div class="dropdown">';
                    echo '<button class="bi bi-three-dots" type="button" id="dropdownMenuButton_' . $row['product_id'] . '" data-bs-toggle="dropdown" aria-expanded="false">';
                    echo '</button>';
                    echo '        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton_' . $row['product_id'] . '">';
                    echo '            <li><a class="dropdown-item edit-product" data-product-id="' . $row['product_id'] . '" data-product-name="' . urlencode($row['name']) . '" data-product-img="' . urlencode($row['product_img']) . '" data-product-price="' . urlencode($row['price']) . '" href="">Edit</a></li>';
                    echo '<li><a class="dropdown-item delete-product" data-product-id="' . $row['product_id'] . '" href="#">Delete</a></li>';
                    echo '        </ul>';         
                echo '    </div>';
            }
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

<?php
$showcase_id = isset($_GET['showcase_id']) ? $_GET['showcase_id'] : '';
$showcase_name = isset($_GET['showcase_name']) ? urldecode($_GET['showcase_name']) : '';
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '';
$owner = isset($_SESSION['name']) ? $_SESSION['name'] : '';

// Check if form is submitted for editing product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $product_id = $_POST["product_id"];

    // Check if product name is provided
    if (!empty($_POST["name"])) {
        $product_name = $_POST["name"];
        // Update product name
        $sql = "UPDATE product SET name = '$product_name' WHERE product_id = '$product_id'";
        if ($conn->query($sql) !== TRUE) {
            echo "Error updating product name: " . $conn->error;
            exit;
        }
    }

    // Check if product price is provided
    if (!empty($_POST["price"])) {
        $product_price = $_POST["price"];
        // Update product price
        $sql = "UPDATE product SET price = '$product_price' WHERE product_id = '$product_id'";
        if ($conn->query($sql) !== TRUE) {
            echo "Error updating product price: " . $conn->error;
            exit;
        }
    }

    // Check if product image is uploaded
    if (!empty($_FILES["product_img"]["name"])) {
        $product_img = $_FILES["product_img"]["name"];
        $targetDir = "./images/"; // You need to create this directory
        $targetFilePath = $targetDir . basename($product_img);
        if (move_uploaded_file($_FILES["product_img"]["tmp_name"], $targetFilePath)) {
            // Update product image
            $sql = "UPDATE product SET product_img = '$targetFilePath' WHERE product_id = '$product_id'";
            if ($conn->query($sql) !== TRUE) {
                echo "Error updating product image: " . $conn->error;
                exit;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    }
    // Reload the page
    echo "<script>window.location.href = 'Showcase-items.php?showcase_id=$showcase_id&showcase_name=$showcase_name&customer_id=$customer_id&owner=$owner';</script>";
    exit();
}
?>


<!-- Modal for editing product -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form method="post" enctype="multipart/form-data" action="Showcase-items.php?showcase_id=<?php echo $showcase_id; ?>&showcase_name=<?php echo urlencode($showcase_name); ?>&showcase_dp=<?php echo $showcase_dp; ?>">
                    <input type="hidden" name="product_id" id="editProductId">
                    <!-- Add hidden input fields to pass showcase_id, showcase_name, and showcase_dp -->
                    <input type="hidden" name="showcase_id" value="<?php echo $showcase_id; ?>">
                    <input type="hidden" name="showcase_name" value="<?php echo $showcase_name; ?>">
                    <input type="hidden" name="showcase_dp" value="<?php echo $showcase_dp; ?>">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label-name">Product Name</label>
                        <input type="text" class="form-control-name" id="editProductName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label-price">Product Price</label>
                        <input type="number" step="0.01" class="form-control-price" id="editProductPrice" name="price" required>
                    </div>
                    <div class="mb-image">
                        <label for="editProductImage" class="form-label-image">Product Image</label>
                        <input type="file" class="form-control-image" id="editProductImage" name="product_img">
                    </div>
                    <!-- Add other input fields for editing -->
                    <div class="modal-footer">
                        <button type="button" class="btn-close-add" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn-update-add" id="editProduct">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

 <!-- Footer -->
 <footer class="text-center text-white" style="background-color: #2d4158">
    <!-- Grid container -->
    <div class="container">
      <!-- Section: Links -->
      <section class="mt-5">
        <!-- Grid row-->
        <div class="row text-center d-flex justify-content-center pt-5">
          <!-- Grid column -->
          <div class="col-md-2">
            <h6 class="text-uppercase font-weight-bold">
              <a href="About.php" class="text-white" style="text-decoration: none">About us</a>
            </h6>
          </div>
          <!-- Grid column -->
          <!-- Grid column -->
          <div class="col-md-2">
            <h6 class="text-uppercase font-weight-bold">
              <a href="FAQ.php" class="text-white" style="text-decoration: none">FAQ's</a>
            </h6>
          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-2">
            <h6 class="text-uppercase font-weight-bold">
              <a href="#!" class="text-white" style="text-decoration: none">Contact</a>
            </h6>
          </div>
          <!-- Grid column -->
        </div>
        <!-- Grid row-->
      </section>
      <!-- Section: Links -->

      <hr class="my-5" />

      <!-- Section: Text -->
      <section class="mb-5">
        <div class="row d-flex justify-content-center">
          <div class="col-lg-8">
            <p>
            Our Company, “UNBOXED” is on a mission to revolutionize self-promotion. Our platform offers users  a dynamic space to showcase their talents, projects, and business in a visually appealing and interactive manner. It’s all about breaking free from the norm and expressing uniqueness. Unboxed is designed to empower individuals who might feel overlooked or underestimated, providing them with a dedicated platform to share their passion and achievements. It’s about going outside the box and letting your creativity shine. Whether you’re an artist, entrepreneur, or innovator, Unboxed is here to help you stand out and make your mark. 
            </p>
          </div>
        </div>
      </section>
      <!-- Section: Text -->

      <!-- Section: Social -->
      <section class="text-center mb-5">
        <a href="Facebook.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-facebook"></i>
        </a>
        <a href="Twitter.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-twitter"></i>
        </a>
        <a href="Google.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-google"></i>
        </a>
        <a href="Instagram.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-instagram"></i>
        </a>
        <a href="LinkedIn.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-linkedin"></i>
        </a>
        <a href="Github.com" class="text-white me-4" style="text-decoration: none">
        <i class="bi bi-github"></i>
        </a>
      </section>
      <!-- Section: Social -->
    </div>
    <!-- Grid container -->

    <!-- Copyright -->
    <div
         class="text-center p-3"
         style="background-color: rgba(0, 0, 0, 0.2)"
         >
      © 2020 Copyright:
      <a class="text-white" href="" style="text-decoration: none"
         >Unboxed</a
        >
    </div>
    <!-- Copyright -->
</footer>
  <!-- Footer -->
</body>
<script>
document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-product');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const productId = this.getAttribute('data-product-id');

                // Send AJAX request to delete_product.php
                fetch(`Showcase-items.php?product_id=${productId}`)
                    .then(response => {
                        if (response.ok) {
                            // Reload the page or handle success as needed
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            // Handle errors
                            console.error('Error deleting product');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting product:', error);
                    });
            });
        });
    });

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