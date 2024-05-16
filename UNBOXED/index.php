<?php
session_start(); // Start session to manage user login state

// Your existing code

if (isset($_POST['open_boxes'])) {
    $_SESSION['open_boxes'] = true; // Set the session variable when "Open the Boxes" button is clicked
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNBOXED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
   <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  </head>
  <body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"  onclick="activateLink(event, this)"></a>
    <div class="navbar-nav" id="navbarLinks">
    <a class="nav-link" href="About.php">ABOUT</a>
    <a class="nav-link" href="FAQ.php">FAQ</a>
  <a class="nav-link" href="Create-Box.php">Sign Up</a>
  <a class="nav-link">|</a>
  <a class="nav-link" href="login.php">Login</a>
</div>
    </div>
  </div>
</nav>

    <div class="bg-logo">
        <img src="./images/bg-logo.PNG" alt="">
    </div>
    <div class="btn">
        <a href="Create-Box.php"><button type="button" class="btn btn-secondary btn-lg">CREATE YOUR BOX</button></a>
        <a href="loader.php"><button type="button" class="btn btn-secondary btn-lg">OPEN THE BOXES</button></a>
        </div>
        <div class="label">
        <span>Create Account</span>
        <span>Guest Mode</span>
        </div>
  </body>
</html>