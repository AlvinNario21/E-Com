<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="create-box-css.css?v=<?php echo time(); ?>">
</head>
<body>
<nav class="backbtn">
<a href="javascript:history.go(-1);"><i class="bi bi-chevron-left"></i></a>
</nav>

    <div class="logo">
        <img src="./images/logo.png" alt="Logo">
        <h1>"TIME TO GET OUT OF THE BOX"</h1>
        <h3>Fill out the box...</h3>
        <div class="container">
            <form id="createForm" action="Home.php" method="post">
                <label for="firstName" class="firstname">HELLO! MY NAME IS <input type="text" id="firstName" name="firstName" placeholder="FIRST NAME" required>
                <input type="text" id="lastName" name="lastName" placeholder="LAST NAME" required></label>
                <label for="field" class="label0">I AM A</label>
                <select name="field" id="field" onchange="showOtherField()" required>
                    <option value="">CHOOSE A FIELD</option>
                    <option value="designer">Designer</option>
                    <option value="developer">Developer</option>
                    <option value="writer">Writer</option>
                    <option value="composer">Composer</option>
                    <option value="freelancer">Freelancer</option>
                    <option value="other">Other</option>
                </select>
                <label for="enthusiast" class="label1">ENTHUSIAST</label>
                <div id="otherField" style="display: none;">
                    <label for="otherFieldInput">Please specify:</label>
                    <input type="text" id="otherFieldInput" name="otherFieldInput">
                </div>
                <label for="skills">WHAT MAKES ME UNIQUE ARE THE SKILLS I HAVE SUCH AS:</label>
                <div class="skills-checkboxes">
                    <label><input type="checkbox" name="skills" value="communication"> Communication</label>
                    <label><input type="checkbox" name="skills" value="creativity"> Creativity</label>
                    <label><input type="checkbox" name="skills" value="problem-solving"> Problem Solving</label>
                    <label><input type="checkbox" name="skills" value="teamwork"> Teamwork</label>
                    <label><input type="checkbox" name="skills" value="flexibly"> Flexible</label>
                    <label><input type="checkbox" name="skills" value="adaptable"> Adaptable</label>
                </div>
               
                <div class="form-group">
                    <label for="exampleInputEmail1">Email</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
            </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
  <br>
  <br>
                <button type="submit" id="submitButton">Create</button>
            </form>
        </div>
    </div>

    <script>
        function showOtherField() {
            var fieldDropdown = document.getElementById("field");
            var otherFieldDiv = document.getElementById("otherField");
            if (fieldDropdown.value === "other") {
                otherFieldDiv.style.display = "block";
                document.getElementById("otherFieldInput").setAttribute("required", "true");
            } else {
                otherFieldDiv.style.display = "none";
                document.getElementById("otherFieldInput").removeAttribute("required");
            }
        }

    // Event listener for form submission
    document.getElementById("createForm").addEventListener("submit", function(event) {
        var firstName = document.getElementById("firstName").value;
        var lastName = document.getElementById("lastName").value;
        var field = document.getElementById("field").value;
        
        if (!firstName || !lastName || !field) {
            event.preventDefault(); // Prevent form submission if any required field is empty
            alert("Please fill out all required fields.");
        } else {
            // If all required fields are filled, redirect to Home.php
            window.location.href = "Home.php";
        }
    });
</script>
    </script>
</body>
</html>