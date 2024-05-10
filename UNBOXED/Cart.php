<?php
session_start(); // Start session to manage user login state

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

if (isset($_GET['delete_item']) && is_numeric($_GET['delete_item'])) {
    $item_id = $_GET['delete_item'];
    
    // SQL to delete the item from the cart
    $delete_sql = "DELETE FROM cart_items WHERE cart_item_id = $item_id";
    
    if ($conn->query($delete_sql) === TRUE) {
        // Item deleted successfully, you can redirect or refresh the page
        header("Location: Cart.php");
        exit;
    } else {
        echo "Error deleting item: " . $conn->error;
    }
}
// Check if the "Check Out" button is clicked
if (isset($_POST['checkout'])) {
    // Get the IDs of the checked items
    $checked_items = isset($_POST['checked_items']) ? $_POST['checked_items'] : array();

    // Check if any items are checked
    if (!empty($checked_items)) {
        // Convert array of IDs to a comma-separated string for URL parameter
        $checked_item_ids = implode(",", $checked_items);

        // Redirect to Checkout.php with the checked item IDs as URL parameter
        header("Location: Checkout.php?items=$checked_item_ids");
        exit;
    } else {
        echo "No items selected for checkout.";
    }
}
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Checkout</title>
        <!-- Option 1: Include in HTML -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
        <!-- Ensure jQuery is included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    </head>
    <body>
    <div class="cart-container">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <div class="nav-logo">
                <a class="navbar-brand" href="Home.php"><img src="./images/logo.png" class="logo" alt="">UNBOXED</a>
                <p class="navbar-brand">|</p>
                <p class="navbar-brand">Checkout</p>
            </div>
            <div class="navbar-nav" id="navbarLinks">
            <a class="nav-link" href="About.php">ABOUT</a>
                <a class="nav-link" href="FAQ.php">FAQ</a>
                <?php if ($displayUserInfo && !empty($name)): ?>
                    <!-- Display user's name and image_dp -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-img"> <?php echo $name; ?>
                        </a>
                        <ul class="dropdown-menu" id="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="Profile.php?customer_id=<?php echo urlencode($customer_id); ?>&name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Profile</a>
                    <a class="dropdown-item" href="Purchase.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">My Purchase</a>
                    <a class="dropdown-item" href="Orders.php?name=<?php echo urlencode($name); ?>&image_dp=<?php echo urlencode($image_dp); ?>">Orders</a>
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

<div class="input-group-cart mb-3">
    <input type="text" class="form-control-cart" id="searchInput" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon2">
    <div class="input-group-append2">
        <i class="bi bi-search" id="searchButton"></i>
    </div>
</div>
<div class="menu">
<div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="selectAllCheckbox">
        <label class="form-check-label-cart" for="selectAllCheckbox">
    Products
  </label>
</div>
<p>Unit Price</p>
  <p>Quantity</p>
  <p>Total Price</p>
  <p>Action</p>
</div>

<form method="POST" action="Checkout.php"> <!-- Added form tag -->
<?php
if (isset($_SESSION["email"]) && isset($_SESSION["customer_id"])) {
    // User is logged in, fetch the user's name and image_dp from the session
    $name = $_SESSION["name"]; // Assuming 'name' is the column name for user's name
    $image_dp = $_SESSION["image_dp"]; // Assuming 'image_dp' is the column name for user's image
    $customer_id = $_SESSION["customer_id"]; // Get the customer_id from the session

    // Modify the SQL query to fetch only items belonging to the logged-in customer
    $sql = "SELECT ci.*, p.product_img, p.name as product_name, p.price as unit_price
            FROM cart_items ci
            INNER JOIN product p ON ci.product_id = p.product_id
            WHERE ci.customer_id = '$customer_id'";
    $result = $conn->query($sql);

    if ($result) {
        // Initialize an array to keep track of products already displayed
        $displayed_products = array();

        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                // Display each cart item
                echo '<div class="product-details">';
                echo '<div class="form-check">';
                echo '<input class="form-check-input" type="checkbox" name="checked_items[]" value="' . $row['cart_item_id'] . '">';
                echo '</div>';
                echo '<img src="' . $row['product_img'] . '" class="product-img" alt="">';
                echo '<div class="cart-product-name">';
                echo '<p class="prod-name">' . $row['product_name'] . '</p>';
                echo '</div>';
                echo '<div class="cart-product-price">';
                echo '<p class="prod-price">₱' . $row['unit_price'] . '</p>';
                echo '</div>';
                echo '<p class="prod-quantity">' . $row['quantity'] . '</p>'; // Output the quantity from the current row
                // Calculate total price
                $total_price = $row['unit_price'] * $row['quantity']; // Use quantity from the current row
                echo '<p class="total-price">₱' . $total_price . '</p>';
                echo '<a class="action" href="Cart.php?delete_item=' . $row['cart_item_id'] . '">Delete</a>';
                echo '</div>';
            }
        } else {
            echo "No items in the cart.";
        }
    } else {
        echo "Error fetching cart items: " . $conn->error;
    }
} else {
    header("Location: login.php");
    exit; // Ensure that subsequent code is not executed after the redirection
}
?>
    <!-- Hidden input field to store the IDs of checked items -->
    <input type="hidden" name="checked_items[]" id="checkedItemsInput">

    <!-- Button trigger modal -->
    <div class="checkout">
        <button type="button" class="btn btn-outline-light" id="checkoutButton">Check Out</button>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">No items selected</h5>
            </div>
            <div class="modal-body">
                Please select at least one item to proceed with checkout.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>
</div>
</body>
<script>
$(document).ready(function() {
        $('#searchButton').click(function() {
            var searchText = $('#searchInput').val().toLowerCase(); // Get the search query and convert to lowercase for case-insensitive search
            var products = $('.product-details'); // Select all elements representing cart items

            // Loop through each cart item
            products.each(function() {
                var productName = $(this).find('.prod-name').text().toLowerCase(); // Get the product name and convert to lowercase

                // Check if the product name contains the search query
                if (productName.includes(searchText)) {
                    $(this).show(); // If the product name matches, show the cart item
                } else {
                    $(this).hide(); // If the product name does not match, hide the cart item
                }
            });
        });
    });

function redirectToProfile(name, image_dp) {
    window.location.href = "Profile.php?name=" + encodeURIComponent(name) + "&image_dp=" + encodeURIComponent(image_dp);
}

 // Get the "Select All" checkbox element
 var selectAllCheckbox = document.getElementById('selectAllCheckbox');

// Add event listener to the "Select All" checkbox
selectAllCheckbox.addEventListener('change', function() {
    // Get all checkboxes in the cart
    var checkboxes = document.querySelectorAll('.form-check-input');

    // Loop through each checkbox and set its checked state
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = selectAllCheckbox.checked;
    });
});

$(document).ready(function() {
        $('#checkoutButton').click(function() {
            // Get all checkboxes in the cart
            var checkboxes = document.querySelectorAll('.form-check-input:checked');

            // Check if any checkbox is checked
            var checkedItems = Array.from(checkboxes).map(function(checkbox) {
                return checkbox.value; // Assuming value attribute contains item ID
            });

            // If no checkbox is checked, show the modal
            if (checkedItems.length === 0) {
                $('#checkoutModal').modal('show');
            } else {
                // Set the value of the hidden input field to the checked item IDs
                $('#checkedItemsInput').val(checkedItems.join(','));

                // Submit the form
                $('form').submit();
            }
        });
    });
</script>

</html>

<?php
$conn->close(); // Close the connection
?>