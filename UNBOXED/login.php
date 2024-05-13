<?php
session_start(); // Start session to manage user login state

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "unboxed";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get user input from login form
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if the user exists in the customer table
    $sql = "SELECT * FROM customer WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists in the customer table, check password
        $row = $result->fetch_assoc();
        if ($row['password'] == $password) {
            // Password is correct, set up session
            $_SESSION["email"] = $email;
            $_SESSION["customer_id"] = $row['customer_id']; // Store customer_id in session
            $_SESSION["name"] = $row['name']; // Assuming 'name' is the column name for user's name
            $_SESSION["image_dp"] = $row['image_dp']; // Assuming 'image_dp' is the column name for user's image
            // Redirect to Home.php
            header("Location: Home.php?customer_id=".$row['customer_id']."&name=".$row['name']);
            exit();
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        // Check if the user exists in the admin table
        $sql = "SELECT * FROM admin WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // User exists in the admin table, check password
            $row = $result->fetch_assoc();
            if ($row['password'] == $password) {
                // Password is correct, set up session
                $_SESSION["email"] = $email;
                $_SESSION["admin_id"] = $row['admin_id']; // Store admin_id in session
                // Redirect to admin.php
                header("Location: admin.php");
                exit();
            } else {
                echo "<script>alert('Incorrect password');</script>";
            }
        } else {
            // Check if the user exists in the driver table
            $sql = "SELECT * FROM driver WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // User exists in the driver table, check password
                $row = $result->fetch_assoc();
                if ($row['password'] == $password) {
                    // Password is correct, set up session
                    $_SESSION["email"] = $email;
                    $_SESSION["driver_id"] = $row['driver_id']; // Store driver_id in session
                    // Redirect to driver.php
                    header("Location: driver.php");
                    exit();
                } else {
                    echo "<script>alert('Incorrect password');</script>";
                }
            } else {
                // User does not exist, you can show a modal or an error message here as well
                // For simplicity, I'm just echoing an error message
                echo "User does not exist";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="login-css.css?v=<?php echo time(); ?>">
</head>
<body>  
    <div class="container">
    <a href="javascript:history.go(-1);"><i class="bi bi-chevron-left"></i></a>
        <div class="row gy-4 align-items-center">
        <div class="col-12 col-md-6 col-xl-7">
          <div class="d-flex justify-content-center text-bg-primary">
            <div class="text-label">
            <div class="col-12 col-xl-9">
              <p class="logo-label"><img class="img-fluid rounded mb-4" loading="lazy" src="./images/logo.png" width="100" height="100" alt="UNBOXED Logo">UNBOXED</p>
              <hr class="border-primary-subtle mb-4">
              <h2 class="h1 mb-4">You make your digital products and we'll drive you to stand out.</h2>
              <p class="lead mb-5">We foster collaboration, growth, and empowerment, ensuring everyone shines in the digital realm.</p>
              </div>
              <div class="text-endx">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-grip-horizontal" viewBox="0 0 16 16">
                  <path d="M2 8a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm3 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm0-3a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-xl-5">
          <div class="card border-0 rounded-4">
            <div class="card-body p-3 p-md-4 p-xl-5">
              <div class="row">
                <div class="col-12">
                  <div class="mb-4">
                    <h3>Sign in</h3>
                  </div>
                </div>
              </div>
              <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <input type="hidden" name="customer_id" value="<?php echo isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : ''; ?>">
              <input type="hidden" name="name" value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>">
                <div class="row gy-3 overflow-hidden">
                  <div class="col-12">
                    <div class="form-floating mb-3">
                      <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                      <label for="email" class="form-label">Email</label>
                    </div>
                  </div>
                    <div class="col-12">
      <div class="form-floating mb-3 position-relative">
          <input type="password" class="form-control" name="password" id="password" value="" placeholder=" " required>
          <i class="bi bi-eye position-absolute top-50 translate-middle-y start-100 translate-middle-x" id="showPassword"></i>
          <i class="bi bi-eye-slash position-absolute top-50 translate-middle-y start-100 translate-middle-x d-none" id="hidePassword"></i>
          <label for="password" class="form-label">Password</label>
      </div>
  </div>

                  <div class="row">
                <div class="col-12">
                  <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-end mt-4">
                  <a href="forgot_password.php" title="Forgot password" class="fg">Forgot password</a>
                  </div>
                </div>
              </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button class="btn btn-primary btn-lg" type="submit" name="login">Login</button>
                    </div>
                  </div>
                </div>
                <p class="signup">Don't have an account? <a href="Create-Box.php" title="Sign up" class="signup-link">Sign up</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </body>
  <script>
    const passwordInput = document.getElementById('password');
    const showPasswordIcon = document.getElementById('showPassword');
    const hidePasswordIcon = document.getElementById('hidePassword');

    // Function to toggle password visibility and icons
    function togglePasswordVisibility() {
        const hasPassword = passwordInput.value !== '';
        if (hasPassword) {
            showPasswordIcon.classList.remove('d-none');
        } else {
            showPasswordIcon.classList.add('d-none');
            hidePasswordIcon.classList.add('d-none');
        }
    }

    // Event listener for showing password
    showPasswordIcon.addEventListener('click', function() {
        passwordInput.type = 'text';
        showPasswordIcon.classList.add('d-none');
        hidePasswordIcon.classList.remove('d-none');
    });

    // Event listener for hiding password
    hidePasswordIcon.addEventListener('click', function() {
        passwordInput.type = 'password';
        hidePasswordIcon.classList.add('d-none');
        showPasswordIcon.classList.remove('d-none');
    });

    // Initially hide the icons if password field is empty
    togglePasswordVisibility();

    // Add input event listener to toggle icon visibility
    passwordInput.addEventListener('input', togglePasswordVisibility);
</script>
  </html>