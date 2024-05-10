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
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
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

<div class="search-name">
<form method="POST" action="Home.php">
    <input type="text"class="input-search" name="searchTerm" placeholder="Search...">
    <button type="submit" name="search" class="btn-search"><i class="bi bi-search"></i></button>
</form>
</div>
<?php if ($cart_item_count !== null && $cart_item_count > 0): ?>
    <div class="count-cart-items">
        <!-- Display cart item count if greater than 0 -->
        <p><?php echo $cart_item_count; ?></p>
    </div>
<?php endif; ?>

<div class="cart">
    <a href="Cart.php"><i class="bi bi-cart4"></i></a>
</div>
<p id="portfolio">OUTSIDE THE BOX</p>

<div class="card-container">
    <?php
    // Initialize search term variable
$searchTerm = "";

// Check if the search form is submitted
if (isset($_POST['search'])) {
    // Get the search term from the form
    $searchTerm = $_POST['searchTerm'];
}

// Query to fetch customer data
$query = "SELECT customer_id, name, image_dp FROM customer";

// Append the search condition to the query
if (!empty($searchTerm)) {
    // If there is already a WHERE clause in the query, append the search condition with AND
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND name LIKE '%$searchTerm%'";
    } else {
        // Otherwise, add a new WHERE clause
        $query .= " WHERE name LIKE '%$searchTerm%'";
    }
}

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Check if the search term matches the name
            if (!empty($searchTerm) && !stristr($row['name'], $searchTerm)) {
                // If the search term does not match the name, continue to the next iteration
                continue;
            }
    
            // Skip displaying the card if the user is logged in
            if (isset($_SESSION["email"]) && isset($_SESSION["customer_id"]) && $row["customer_id"] === $_SESSION["customer_id"]) {
                continue;
            }
    
            // Display the card
            echo '<div class="card" style="width: 18rem;" onclick="redirectToProfile(\'' . urlencode($row["name"]) . '\', \'' . urlencode($row["image_dp"]) . '\')">'; 
            echo '<a href="Profile.php?from_card=true&customer_id=' . $row["customer_id"] . '">'; 
            echo '<img src="' . $row["image_dp"] . '" class="card-img-top" alt="' . $row["name"] . '">';
            echo '</a>';
            echo '<div class="card-body">';
            echo '<p class="card-text">' . $row["name"] . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "0 results";
    }       
    ?>
</div>


<p id="showcase-con">SHOWCASE OF BOXES</p>
<div class="showcase-con">
<?php
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

// Initialize search term variable
$searchTerm = "";

// Check if the search form is submitted
if (isset($_POST['search'])) {
    // Get the search term from the form
    $searchTerm = $_POST['searchTerm'];
}

// Query to fetch showcase data excluding showcases owned by the logged-in user
$query = "SELECT showcase_id, showcase_name, owner, showcase_dp FROM showcase";

// Check if the user is logged in
if (isset($_SESSION["email"])) {
    // User is logged in, fetch the user's customer_id from the session
    $customer_id = isset($_SESSION["customer_id"]) ? $_SESSION["customer_id"] : '';

    // Exclude showcases owned by the logged-in user
    if (!empty($customer_id)) {
        $query .= " WHERE owner NOT IN (SELECT name FROM customer WHERE customer_id = '$customer_id')";
    }
}

// Append the search condition to the query
if (!empty($searchTerm)) {
    // If there is already a WHERE clause in the query, append the search condition with AND
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND (showcase_name LIKE '%$searchTerm%' OR owner LIKE '%$searchTerm%')";
    } else {
        // Otherwise, add a new WHERE clause
        $query .= " WHERE (showcase_name LIKE '%$searchTerm%' OR owner LIKE '%$searchTerm%')";
    }
}

$result = mysqli_query($conn, $query);

// Check if query executed successfully
if ($result) {
    // Loop through each row in the result set
    while ($row = mysqli_fetch_assoc($result)) {
        // Output HTML for each showcase card
        echo '<div class="showcase-card" style="width: 18rem;">';
        // Pass a parameter in the URL to indicate navigation from Home.php
        echo '<a href="Showcase-items.php?showcase_id=' . urlencode($row['showcase_id']) . '&showcase_name=' . urlencode($row['showcase_name']) . '&from_home=true">';
        echo '<img class="showcase-card-img" src="' . $row['showcase_dp'] . '" alt="Card image cap">';
        echo '<div class="showcase-card-body">';
        echo '<h5 class="card-showcase_name">' . $row['showcase_name'] . '</h5>';
        echo '<h6 class="card-owner">' . $row['owner'] . '</h6>';
        echo '</div>';
        echo '</div>';
        echo '</a>';
    }
    // Free result set
    mysqli_free_result($result);
} else {
    // Handle error if query fails
    echo "Error: " . mysqli_error($connection);
}
?>

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
              <a href="Contact.php" class="text-white" style="text-decoration: none">Contact</a>
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
function redirectToProfile(name, image_dp) {
        window.location.href = "Profile.php?name=" + encodeURIComponent(name) + "&image_dp=" + encodeURIComponent(image_dp);
    }
    document.querySelectorAll('.showcase-card').forEach(card => {
        card.addEventListener('click', function() {
            const showcaseId = this.getAttribute('data-showcase-id');
            const showcaseName = this.getAttribute('data-showcase-name');
            window.location.href = `Showcase-items.php?showcase_id=${encodeURIComponent(showcaseId)}&showcase_name=${encodeURIComponent(showcaseName)}`;
        });
    });
</script>

</html>

<?php
$conn->close(); // Close the connection
?>
