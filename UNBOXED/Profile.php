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

    // Redirect to the login page or any other desired page
    header("Location: login.php");
    exit;
}

// Fetch name and image_dp from the customer table
$sql = "SELECT customer_id, name, image_dp FROM customer";
$result = $conn->query($sql);

// Retrieve the user's name and profile picture from URL parameters
if(isset($_GET['name']) && isset($_GET['image_dp'])) {
    $name = $_GET['name'];
    $image_dp = $_GET['image_dp'];

} else {
    // Handle case where parameters are not provided
    $name = "User";
    $image_dp = ""; // Set a default image if needed
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
            <?php if (!empty($name)): ?>
                <a class="navbar-brand">|</a>
                <a class="navbar-brand"><?php echo $name; ?></a>
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

<div class="profile-con">
    <?php if (!empty($image_dp) || empty($name)): ?>
        <!-- Display profile background and picture if available or in guest mode -->
        <img src="./images/prof-bg.png" alt="" class="profile-bg">
        <?php if (!empty($image_dp)): ?>
            <!-- Display user profile picture if available -->
            <img src="<?php echo $image_dp; ?>" alt="<?php echo $name; ?>" class="profile-pic">
        <?php endif; ?>
    <?php else: ?>
        <!-- Display default background if no profile picture available -->
        <img src="./images/prof-bg.png" alt="" class="profile-bg">
    <?php endif; ?>
</div>

<!-- Display "My Portfolio" section regardless of user login state -->
<div class="prof-info">
    <div class="port-label">
        <p>My Portfolio</p>
        <div class="portfolio">
            <!-- Include portfolio content here -->
        </div>
    </div>
    <p class="lbl">Showcase</p>
    <div class="showcase-con">
        <div class="showcase-card-container">
            <?php
// Fetch the customer ID based on the provided name (if available)
if(isset($_GET['name']) && isset($_GET['image_dp'])) {
    $name = $_GET['name'];
    $image_dp = $_GET['image_dp'];

    // Fetch the customer ID based on the provided name
    $customer_query = "SELECT customer_id FROM customer WHERE name = '$name'";
    $customer_result = $conn->query($customer_query);

    if ($customer_result->num_rows > 0) {
        $customer_row = $customer_result->fetch_assoc();
        $customer_id = $customer_row['customer_id'];

        // Fetch showcase data based on the retrieved customer ID
        $showcase_query = "SELECT showcase_name, showcase_dp FROM showcase WHERE customer_id = $customer_id";
        $showcase_result = $conn->query($showcase_query);

        if ($showcase_result->num_rows > 0) {
            // Display the showcase data
            while ($showcase_row = $showcase_result->fetch_assoc()) {
                echo '<a href="Showcase.php?name=' . urlencode($showcase_row['showcase_name']) . '&image_dp=' . urlencode($showcase_row['showcase_dp']) . '">';
                echo '<div class="showcase-item">';
                echo '<h3>' . $showcase_row["showcase_name"] . '</h3>';
                echo '<img src="' . $showcase_row["showcase_dp"] . '" alt="' . $showcase_row["showcase_name"] . '">';
                echo '</div>';
            }
        } else {
            echo "No showcase items found for this customer.";
        }
    } else {
        echo "Customer not found.";
    }
} else 
    // Check if the card is clicked and the customer_id is provided
if(isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];

    // Fetch showcase data based on the provided customer_id
    $showcase_query = "SELECT showcase_name, showcase_dp FROM showcase WHERE customer_id = $customer_id";
    $showcase_result = $conn->query($showcase_query);

    if ($showcase_result->num_rows > 0) {
        // Display the showcase data
        while ($showcase_row = $showcase_result->fetch_assoc()) {
            echo '<a href="Showcase.php?name=' . urlencode($showcase_row['showcase_name']) . '&image_dp=' . urlencode($showcase_row['showcase_dp']) . '">';
            echo '<div class="showcase-item">';
            echo '<h3>' . $showcase_row["showcase_name"] . '</h3>';
            echo '<img src="' . $showcase_row["showcase_dp"] . '" alt="' . $showcase_row["showcase_name"] . '">';
            echo '</div>';
        }
    } else {
        echo "No showcase items found for this customer.";
    }
} else {
    echo "Customer ID is not provided.";
}


            ?>
        </div>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>