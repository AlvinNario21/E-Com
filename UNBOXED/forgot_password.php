<?php
session_start();

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

    // Get user input from forgot password form
    $email = $_POST["email"];

    // SQL query to check if the user exists
    $sql = "SELECT * FROM customer WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, proceed to the next modal for password reset
        echo '<script>window.location.href = "reset_password.php?email='.$email.'";</script>';
    } else {
        // User does not exist, show an error message or handle it as needed
        echo "<script>alert('Email not found');</script>";
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
    <div class="forgot-container">
    <p class="forgot-label">Forgot Password</p>
        <!-- Forgot password form -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-floating mb-3">
                <input type="email" class="form-control-email" name="email" id="email" placeholder="Email" required>
                <label for="email" class="form-label-email">Email</label>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary btn-lg" type="submit" name="forgot_password">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
