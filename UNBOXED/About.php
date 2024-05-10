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
    <a href="javascript:history.go(-1);"><i class="bi bi-chevron-left"></i></a>
        <div class="nav-logo">
            <a class="navbar-brand" href="Home.php"><img src="" class="" alt=""></a>
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

<section class="py-3 py-md-5">
    <div class="container">
      <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
        <div class="col-12 col-lg-6 col-xl-5">
          <img class="img-fluid rounded" loading="lazy" src="./images/bg-logo.png" alt="logo">
        </div>
        <div class="col-12 col-lg-6 col-xl-7">
          <div class="row justify-content-xl-center">
            <div class="col-12 col-xl-11">
              <h2 class="mb-3">About Us</h2>
              <p class="lead fs-4 text-secondary mb-3">We strive to create a global community where creativity flourishes, offering a user-friendly platform to connect like-minded individuals for collaboration, inspiration, and growth.</p>
              <p class="mb-5">We see UNBOXED's future as a symbol of empowerment, enabling people worldwide to confidently express their talents and passions. We're committed to nurturing a global community where creativity is limitless, ensuring everyone, regardless of background, can shine.</p>
              <div class="row gy-4 gy-md-0 gx-xxl-5X">
                <div class="col-12 col-md-6">
                  <div class="d-flex">
                    <div class="me-4 text-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                        <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                      </svg>
                    </div>
                    <div>
                      <h2 class="h4 mb-3">Versatile Brand</h2>
                      <p class="text-secondary mb-0">We are crafting a digital method that subsists life across all mediums.</p>
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="d-flex">
                    <div class="me-4 text-primary">
                      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-fire" viewBox="0 0 16 16">
                        <path d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16Zm0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15Z" />
                      </svg>
                    </div>
                    <div>
                      <h2 class="h4 mb-3">Digital Agency</h2>
                      <p class="text-secondary mb-0">We believe in innovation by merging primary with elaborate ideas.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="site-section border-bottom" data-aos="fade">
        <div class="container">
          <div class="row justify-content-center mb-5">
            <div class="col-md-7 site-section-heading text-center pt-4">
              <h2>Meet the Team</h2>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-lg-3">
    
              <div class="block-38 text-center">
                <div class="block-38-img">
                  <div class="block-38-header">
                    <img src="./images/trina.jpg" alt="Image placeholder" class="mb-4">
                    <h3 class="block-38-heading h4">Trina Chariz B. Chariz</h3>
                    <p class="block-38-subheading">Project Manager</p>
                  </div>
                  <div class="block-38-body">
                    <p>A graduating student, passionate about delivering high-quality projects on time while fostering a collaborative and inclusive team environment. Seeking to leverage my expertise in Information Technology to drive innovation and achieve organizational goals. </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="block-38 text-center">
                <div class="block-38-img">
                  <div class="block-38-header">
                    <img src="./images/hazel.jpg" alt="Image placeholder" class="mb-4">
                    <h3 class="block-38-heading h4">Hazel Anne B. Marqueses</h3>
                    <p class="block-38-subheading">System Analyst</p>
                  </div>
                  <div class="block-38-body">
                    <p>Passionate about crafting engaging web experiences, I bring technical prowess, attention to detail, and a collaborative mindset to create seamless digital solutions. With a strong background on leadership and drive for excellence, I thrive both independently and within dynamic teams, dedicated to exceeding expectations and delivering exceptional results.
</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="block-38 text-center">
                <div class="block-38-img">
                  <div class="block-38-header">
                    <img src="./images/rachelle.jpg" alt="Image placeholder" class="mb-4">
                    <h3 class="block-38-heading h4">Rachelle Anne B. Manila</h3>
                    <p class="block-38-subheading">Frontend Developer</p>
                  </div>
                  <div class="block-38-body">
                    <p>Passionate UI/UX Designer and Graphic Designer with a strong foundation in user-centered design principles and visual communication. Proven ability to collaborate effectively with cross-functional teams to create user-friendly and visually appealing interfaces, branding materials, and marketing assets.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="block-38 text-center">
                <div class="block-38-img">
                  <div class="block-38-header">
                    <img src="./images/alvin.jpg" alt="Image placeholder" class="mb-4">
                    <h3 class="block-38-heading h4">Alvin S. Nario</h3>
                    <p class="block-38-subheading">Backend Developer</p>
                  </div>
                  <div class="block-38-body">
                    <p>I'm a full-stack developer with experience in both front-end and back-end. I enjoy crafting user-friendly interfaces and building dynamic web applications. I'm skilled in project planning to deliver robust solutions.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="site-footer border-top">
        <div class="container">
          <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
              <div class="row">
                <div class="col-md-12">
                  <h3 class="footer-heading mb-4">Navigations</h3>
                </div>
                <div class="col-md-6 col-lg-4">
                  <ul class="list-unstyled">
                    <li><a href="#">Sell online</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Shopping cart</a></li>
                    <li><a href="#">Store builder</a></li>
                  </ul>
                </div>
                <div class="col-md-6 col-lg-4">
                  <ul class="list-unstyled">
                    <li><a href="#">Mobile commerce</a></li>
                    <li><a href="#">Dropshipping</a></li>
                    <li><a href="#">Website development</a></li>
                  </ul>
                </div>
                <div class="col-md-6 col-lg-4">
                  <ul class="list-unstyled">
                    <li><a href="#">Point of sale</a></li>
                    <li><a href="#">Hardware</a></li>
                    <li><a href="#">Software</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="block-5 mb-5">
                <h3 class="footer-heading mb-4">Contact Info</h3>
                <ul class="list-unstyled">
                  <li class="address">203 Fake St. Mountain View, San Francisco, California, USA</li>
                  <li class="phone"><a href="tel://23923929210">+2 392 3929 210</a></li>
                  <li class="email">emailaddress@domain.com</li>
                </ul>
              </div>
  
              <div class="block-7">
                <form action="#" method="post">
                  <label for="email_subscribe" class="footer-heading">Subscribe</label>
                  <div class="form-group">
                    <input type="text" class="form-control py-4" id="email_subscribe" placeholder="Email">
                    <input type="submit" class="btn btn-sm btn-primary" value="Send">
                  </div>
                </form>
              </div>
            </div>
          </div>
      </footer>
    </div>

  </section>

</div>
</body>
<script>
function redirectToProfile(name, image_dp) {
    window.location.href = "Profile.php?name=" + encodeURIComponent(name) + "&image_dp=" + encodeURIComponent(image_dp);
}
</script>

</html>

<?php
$conn->close(); // Close the connection
?>
