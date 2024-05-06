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
$customer_id = null; // Initialize $customer_id

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
        $customer_id = $_SESSION["customer_id"]; // Assign customer_id here
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

            // Check if the product already exists in the cart for the user
            $existing_item_query = "SELECT * FROM cart_items WHERE customer_id = '$customer_id' AND product_id = '$product_id'";
            $existing_item_result = $conn->query($existing_item_query);

            if ($existing_item_result && $existing_item_result->num_rows > 0) {
                // Product already exists in cart, update the quantity
                $existing_item_row = $existing_item_result->fetch_assoc();
                $existing_quantity = $existing_item_row['quantity'];
                $new_quantity = $existing_quantity + $quantity;

                // Update the quantity in cart_items table
                $update_sql = "UPDATE cart_items SET quantity = '$new_quantity' WHERE customer_id = '$customer_id' AND product_id = '$product_id'";
                if ($conn->query($update_sql) === TRUE) {
                    // Redirect to Cart.php after updating the cart
                    header("Location: Cart.php");
                    exit;
                } else {
                    echo "Error updating quantity: " . $conn->error;
                }
            } else {
                // Product does not exist in cart, insert a new row
                $insert_sql = "INSERT INTO cart_items (customer_id, product_id, product_img, product_name, unit_price, quantity) VALUES ('$customer_id', '$product_id', '$product_img', '$product_name', '$product_price', '$quantity')";
                if ($conn->query($insert_sql) === TRUE) {
                    // Redirect to Cart.php after adding to cart
                    header("Location: Cart.php");
                    exit;
                } else {
                    echo "Error adding to cart: " . $conn->error;
                }
            }
        } else {
            echo "No products available.";
        }
    } else {
        echo "Error: Product details or customer ID are incomplete.";
    }
}

// Fetch the customer_id from the product table based on the product_id
$product_customer_id = '';
if (!empty($product_id)) {
    $customer_id_query = "SELECT customer_id FROM product WHERE product_id = '$product_id'";
    $customer_id_result = $conn->query($customer_id_query);
    if ($customer_id_result && $customer_id_result->num_rows > 0) {
        $customer_id_row = $customer_id_result->fetch_assoc();
        $product_customer_id = $customer_id_row['customer_id'];
    }
}

// Compare the product's customer_id with the logged-in user's customer_id
$disable_buttons = false;
if ($product_customer_id === $customer_id) {
    $disable_buttons = true;
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
<?php if ($cart_item_count !== null && $cart_item_count > 0): ?>
    <div class="count-cart-items">
        <!-- Display cart item count if greater than 0 -->
        <p><?php echo $cart_item_count; ?></p>
    </div>
<?php endif; ?>
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
// Check if the user is logged in
if (isset($_SESSION["customer_id"])) {
    // Retrieve the customer's ID from the session
    $customerId = $_SESSION["customer_id"];

    // Query to fetch the shipping address based on the customer's ID
    $addressQuery = "SELECT address FROM customer WHERE customer_id = ?";
    
    // Prepare and execute the statement
    $stmt = $conn->prepare($addressQuery);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    
    // Bind the result variables
    $stmt->bind_result($address);
    
    // Fetch the result
    $stmt->fetch();
    
    // Close the statement
    $stmt->close();
    
    // Display the shipping address if found
    if ($address) {
        echo "<p>$address</p>";
    } else {
        echo "<p>No shipping address found.</p>";
    }
} else {
    echo "<p>User not logged in.</p>";
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
        <input type="text" id="quantity" name="quantity" min="1" value="1">
        <i id="increment" class="bi bi-plus"></i>
        <p>10 pieces available</p>
    </div>
    <div class="prod-button">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
    <button type="submit" name="addToCartBtn" class="btn btn-outline-light" <?php echo $disable_buttons ? 'disabled' : ''; ?>><i class="bi bi-cart-plus"></i> Add To Cart</button>
    <button type="button" class="btn btn-secondary" <?php echo $disable_buttons ? 'disabled' : ''; ?>>Buy Now</button>
</div>
</div>
</form>
<div class="description-con">
    <p class="desc-label">Product Description</p>
    <p class="description-text"><?php echo isset($_GET['product_description']) ? $_GET['product_description'] : ''; ?></p>
</div>
<?php
// Query to fetch reviews for the given product_id along with customer name, image, and review date
$reviews_query = "SELECT r.rating, r.review_text, r.review_date, c.name AS customer_name, c.image_dp AS customer_img
                  FROM reviews r
                  INNER JOIN customer c ON r.customer_id = c.customer_id
                  WHERE r.product_id = '$product_id'";
$reviews_result = $conn->query($reviews_query);

// Check if there are reviews available
if ($reviews_result && $reviews_result->num_rows > 0) {
    // Output the reviews
    echo '<div class="product-reviews">';
    echo '<p class="rev-label">Product Ratings</p>';
    // Loop through each review
    while ($review_row = $reviews_result->fetch_assoc()) {
        // Display star rating based on numeric rating
        $rating = $review_row['rating'];
        echo '<div class="star-rating">';
        echo '<p class="cust-label">' . $review_row['customer_name'] . '</p>';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                echo '<i class="bi bi-star-fill"></i>'; // Full star
            } else {
                echo '<i class="bi bi-star"></i>'; // Empty star
            }
        }
        echo '</div>';
        echo '<div class="reviews-details">';
        // Display customer name, image, and review date
        echo '<img src="' . $review_row['customer_img'] . '" alt="' . $review_row['customer_name'] . '" class="customer-img">';
        echo '<p class="review_date">' . $review_row['review_date'] . '</p>';
        echo '<p class="review_text">' . $review_row['review_text'] . '</p>';

         echo '</div>';
    }
    
    echo '</div>'; // Close product-reviews div
} else {
    // No reviews found for the product
    echo 'No Ratings Available';
}
?>
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
