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

    // SQL query to check if the user exists
    $sql = "SELECT * FROM customer WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // If user exists, set up session
        $row = $result->fetch_assoc();
        $_SESSION["email"] = $email;
        $_SESSION["customer_id"] = $row['customer_id']; // Store customer_id in session
        $_SESSION["name"] = $row['name']; // Assuming 'name' is the column name for user's name
        $_SESSION["image_dp"] = $row['image_dp']; // Assuming 'image_dp' is the column name for user's image
        header("Location: Home.php");
        exit();
    } else {
    // Incorrect password alert with close button
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>

              Incorrect password
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';

}
$conn->close(); // Close the connection
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

<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  
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
                    <a href="#!" title="Forgot password">Forgot password</a>
                  </div>
                </div>
              </div>
                  <div class="col-12">
                    <div class="d-grid">
                      <button class="btn btn-primary btn-lg" type="submit" name="login">Login</button>
                    </div>
                  </div>
                </div>
                <p class="signup">Don't have an account? <a href="Create-Box.php" title="Sign up">Sign up</a></p>
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