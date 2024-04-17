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

// Check if the user is logged in
if (isset($_SESSION["email"])) {
    // User is logged in, fetch the user's name and image_dp from the session
    $email = $_SESSION["email"]; // Retrieve the user's email from the session

    // Fetch the corresponding customer_id from the database based on the email
    $sql = "SELECT customer_id, name, image_dp FROM customer WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Store customer_id, name, and image_dp in the session
        $_SESSION["customer_id"] = $row["customer_id"];
        $_SESSION["name"] = $row["name"];
        $_SESSION["image_dp"] = $row["image_dp"];
        
        // Assign fetched values to variables
        $name = $_SESSION["name"];
        $image_dp = $_SESSION["image_dp"];
    } else {
        echo "No customer available.";
    }
} else {
    // User is not logged in, set default values
    $name = "";
    $image_dp = "";
}

// Check if the session variable is set and true (meaning "Open the Boxes" button is clicked)
$displayUserInfo = !(isset($_SESSION['open_boxes']) && $_SESSION['open_boxes']);

$product_id = '';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
}


// Check if the 'Add to Cart' button is clicked
if (isset($_POST['addToCartBtn'])) {
    // Get product details from the form
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
    // Retrieve customer ID from session
    $customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '';

    // Ensure that essential product details and customer ID are provided
    if (!empty($product_id) && !empty($customer_id)) {
        // Retrieve product details based on product_id
        $sql = "SELECT * FROM product WHERE product_id = '$product_id'";
        $product_result = $conn->query($sql);

        // Debugging: Output SQL query to check if it's correct
        echo "SQL Query: " . $sql . "<br>";

        if ($product_result && $product_result->num_rows > 0) {
            // Fetch product details
            $row = $product_result->fetch_assoc();
            $product_img = $row['product_img'];
            $product_name = $row['name'];
            $product_price = $row['price'];
            $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1; // Default quantity to 1 if not provided

            // Insert into cart_items table
            $insert_sql = "INSERT INTO cart_items (customer_id, product_id, product_img, product_name, unit_price, quantity) VALUES ('$customer_id', '$product_id', '$product_img', '$product_name', '$product_price', '$quantity')";
            if ($conn->query($insert_sql) === TRUE) {
                // Redirect to Cart.php after adding to cart
                header("Location: Cart.php");
                exit;
            } else {
                echo "Error: " . $insert_sql . "<br>" . $conn->error;
            }
        } else {
            echo "No products available.";
        }
    } else {
        echo "Error: Product details or customer ID are incomplete.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNBOXED</title>
    <!-- Option 1: Include in HTML -->
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
            <?php if (!empty($name)): ?>
                <a class="navbar-brand">|</a>
                <a class="navbar-brand"><?php echo $name; ?></a>
            <?php endif; ?>
        </div>
        <div class="navbar-nav" id="navbarLinks">
            <a class="nav-link" href="#">ABOUT</a>
            <a class="nav-link" href="#">FAQ</a>
            <?php if ($displayUserInfo && !empty($name)): ?>
                <!-- Display user's name and image_dp -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="Profile.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">Profile</a>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="?logout=1">Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
               <!-- If user is not logged in or "Open the Boxes" button is clicked, display Sign Up and Login links -->
                <a class="nav-link" href="Create-Box.php">Sign Up</a>
                <a class="nav-link">|</a>
                <a class="nav-link" href="login.php">Login</a>
            <?php endif; ?>

        </div>
    </div>
</nav>

<div class="input-group mb-3">
    <input type="text" class="form-control" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2">
    <div class="input-group-append">
        <i class="bi bi-search"></i>
    </div>
</div>
<div class="count-cart-items">
    <?php if ($cart_item_count > 0): ?>
        <!-- Display cart item count if greater than 0 -->
        <p><?php echo $cart_item_count; ?></p>
    <?php endif; ?>
</div>
<div class="cart">
    <a href="Cart.php"><i class="bi bi-cart4" href="Cart.php"></i></a>
</div>
<form method="POST" action="Product.php"> 
<div class="items-container">
    <img src="<?php echo isset($_GET['product_img']) ? $_GET['product_img'] : ''; ?>" class="items-img" alt="">
</div>
<div class="items-card">
    <img src="<?php echo isset($_GET['product_img']) ? $_GET['product_img'] : ''; ?>" class="items-img-2" alt="">
</div>
<div class="item-description">
    <p class="item-name"><?php echo isset($_GET['product_name']) ? $_GET['product_name'] : ''; ?></p>
    <div class="items-rate">
        <p>10 Ratings</p>
        <p>|</p>
        <p>10 Sold</p>
    </div>
    <div class="items-price">
        <p class="price"><?php echo isset($_GET['product_price']) ? '₱' . $_GET['product_price'] : ''; ?></p>
    </div>
    <div class="items-shipping">
        <p>Shipping</p>
        <p><i class="bi bi-truck"></i></p>
        <p>Shipping To</p>
        <?php
// Assuming you already have a database connection established

// Query to fetch addresses from the address table
$sql = "SELECT address_id, barangay, municipality, province FROM address";
$result = $conn->query($sql);

// Check if there are addresses available
if ($result && $result->num_rows > 0) {
    echo '<select class="select-address" aria-label="Default select example">';
    echo '<option selected>Choose an address</option>';
    
    // Loop through each row in the result set
    while ($row = $result->fetch_assoc()) {
        // Generate an option element for each address
        echo '<option value="' . $row['address_id'] . '">';
        echo $row['barangay'] . ', ' . $row['municipality'] . ', ' . $row['province'];
        echo '</option>';
    }
    
    echo '</select>';
} else {
    echo '<p>No addresses found.</p>';
}
?>
    </div>
    <div class="items-shipping">
        <p class="fee">Shipping Fee</p>
        <p>₱55</p>
    </div>
    <div class="items-quantity">
        <p>Quantity</p>
        <i id="decrement" class="bi bi-dash"></i>
        <input type="text" id="quantity" min="1" value="1">
        <i id="increment" class="bi bi-plus"></i>
        <p>10 pieces available</p>
    </div>
    <div class="prod-button">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
    <button type="submit" name="addToCartBtn" class="btn btn-outline-light"><i class="bi bi-cart-plus"></i> Add To Cart</button>
    <button type="button" class="btn btn-secondary">Buy Now</button>
</div>
</div>
</form>
<div class="description-con">
    <p>Product Description</p>
    <p class="description-text"><?php echo isset($_GET['product_description']) ? $_GET['product_description'] : ''; ?></p>
</div>

</body>
<script>
function redirectToProfile(name, image_dp) {
    window.location.href = "Profile.php?name=" + encodeURIComponent(name) + "&image_dp=" + encodeURIComponent(image_dp);
}

document.addEventListener("DOMContentLoaded", function() {
    // Get quantity input element
    const quantityInput = document.getElementById('quantity');

    // Get increment and decrement icons
    const incrementIcon = document.getElementById('increment');
    const decrementIcon = document.getElementById('decrement');

    // Add click event listener to increment icon
    incrementIcon.addEventListener('click', function() {
        // Get current quantity value
        let quantity = parseInt(quantityInput.value);
        // Increment quantity
        quantity++;
        // Update input value
        quantityInput.value = quantity;
    });

    // Add click event listener to decrement icon
    decrementIcon.addEventListener('click', function() {
        // Get current quantity value
        let quantity = parseInt(quantityInput.value);
        // Decrement quantity if greater than 1
        if (quantity > 1) {
            quantity--;
            // Update input value
            quantityInput.value = quantity;
        }
    });
});
</script>
</html>

<?php
$conn->close(); // Close the connection
?>
