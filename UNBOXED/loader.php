<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>UNBOXED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="loadercss.css?v=<?php echo time(); ?>">
</head>
<body>
<div class="preloader">
	<div class="box">
		<div class="box__inner">
			<div class="box__back-flap"></div>
			<div class="box__right-flap"></div>
			<div class="box__front-flap"></div>
			<div class="box__left-flap"></div>
			<div class="box__front"></div>
		</div>
	</div>
	<div class="box">
		<div class="box__inner">
			<div class="box__back-flap"></div>
			<div class="box__right-flap"></div>
			<div class="box__front-flap"></div>
			<div class="box__left-flap"></div>
			<div class="box__front"></div>
		</div>
	</div>
	<div class="line">
		<div class="line__inner"></div>
	</div>
	<div class="line">
		<div class="line__inner"></div>
	</div>
	<div class="line">
		<div class="line__inner"></div>
	</div>
	<div class="line">
		<div class="line__inner"></div>
	</div>
	<div class="line">
		<div class="line__inner"></div>
	</div>
	<div class="line">
		<div class="line__inner"></div>
	</div>
	<div class="line">
		<div class="line__inner"></div>
	</div>
<p class="load-label">Unboxing...</p>
   <!-- Loading bar -->
<div id="loading-bar">
    <div id="loading-progress"></div>
</div>
</div>
</body>
<script>
    // Simulate loading progress and redirect after content is fully loaded
    document.addEventListener("DOMContentLoaded", function() {
        let progress = 0;
        let loadingInterval = setInterval(function() {
            progress += Math.random() * 5; // Simulate random progress
            if (progress >= 100) {
                clearInterval(loadingInterval);
                // Redirect to the home page after a short delay to simulate content loading
                setTimeout(function() {
                    window.location.href = 'Home.php';
                }, 500); // Adjust the delay here
            }
            document.getElementById("loading-progress").style.width = progress + "%";
        }, 100); // Adjust the interval here
    });
</script>
</html>