<?php
// Verifica se il dominio corrente è diverso da zenchat.it
if ($_SERVER['HTTP_HOST'] !== 'kloe.zenchat.it') {
    // Effettua la redirezione
    header('Location: https://kloe.zenchat.it/');
    exit();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="fonts/icomoon/style.css">

    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">

    <title>Login Zenchat</title>
  </head>
  <body>

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('images/bg_1.jpg');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
             <span class="ml-auto"><a href="https://zenchat.it" target="_blank" class="forgot-pass">Home</a></span> 
<div class="mb-4">
    <h3>Sign In <img style="width:70px; border-radius:10px 10px 10px;" src="images/kloe.gif" alt="homepage" class="dark-logo" /></h3>
    <p class="mb-4">Zenchat Virtual Assistant.</p>
    <?php if ($_GET['errore'] == "login") { ?>
        <p style="color:red;">Error Login. Username Or Password.</p>
    <?php } ?>
    <p>Don't have an account? <a href="#" id="registerLink">Register</a></p>
    <?php if (!!$_GET['stato']) { ?>
    <p style="font-size: 20px; color:red;"><?php echo $_GET['stato']; ?></p>
    <?php } ?>
</div>
            <form action="login.php" method="post">
              <div class="form-group first">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username">

              </div>
              <div class="form-group last mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">
                
              </div>
                        <div class="d-flex mb-5 align-items-center">
                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                  <input type="checkbox" checked="checked"/>
                  <div class="control__indicator"></div>
                </label>
              <span class="ml-auto"><a href="#" class="forgot-pass" id="forgotPass">Forgot Password</a></span> 
              </div>

              <input type="submit" value="Log In" class="btn btn-block btn-primary">

               <!--  <span class="d-block text-center my-4 text-muted">&mdash; or &mdash;</span>
              
           <div class="social-login">
                <a href="#" class="facebook btn d-flex justify-content-center align-items-center">
                  <span class="icon-facebook mr-3"></span> Login with Facebook
                </a>
                <a href="#" class="twitter btn d-flex justify-content-center align-items-center">
                  <span class="icon-twitter mr-3"></span> Login with  Twitter
                </a>
                <a href="#" class="google btn d-flex justify-content-center align-items-center">
                  <span class="icon-google mr-3"></span> Login with  Google
                </a>
              </div>-->
            </form>
          </div>
        </div>
      </div>
    </div>

    
  </div>
    
    <!-- Aggiungi questo prima della chiusura del tag </body> -->
<div class="modal" tabindex="-1" role="dialog" id="registerModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
<form id="registerForm" action="https://kloe.zenchat.it/ajax/insertUser.php" method="post">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="registerPassword">Password</label>
        <input type="password" class="form-control" id="registerPassword" name="password" required>
    </div>
    <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="privacyCheck" required>
        <label class="form-check-label" for="privacyCheck">I agree to the privacy policy</label>
    </div>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

            </div>
        </div>
    </div>
</div>


    <!-- Aggiungi questo prima della chiusura del tag </body> -->
<div class="modal" tabindex="-1" role="dialog" id="lostPass">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Forgot Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
<form id="lostForm" action="https://kloe.zenchat.it/ajax/lostPass.php" method="post">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <button type="submit" class="btn btn-primary">Send</button>
</form>

            </div>
        </div>
    </div>
</div>


    <script src="js/jquery-3.3.1.min.js"></script>

<!-- Aggiungi questo script jQuery alla fine del tuo HTML -->
<script>
    $(document).ready(function () {
        $("#registerLink").click(function () {
            // Show the registration popup
            $("#registerModal").modal('show');
        });

        // Handle registration form submission via AJAX
        $("#registerForm").submit(function (e) {
            e.preventDefault(); // Prevent normal form submission

            // Check if passwords match
            var password = $("#registerPassword").val();
            var confirmPassword = $("#confirmPassword").val();

            if (password !== confirmPassword) {
                alert("Passwords do not match. Please confirm your password.");
                return;
            }

            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: $(this).serialize(),
                success: function (response) {
                    console.log("Server Response:", response);
                    // Handle the server response
                    if (response === '{"success":true,"message":"success"}') {
                        // Registration successful, show a success alert
                        alert("Registration successful! Check your email inbox to activate your account.");
                        // Close the registration popup
                        $("#registerModal").modal('hide');
                    } else {
                        // Otherwise, show an error alert
                        alert("Error during registration. You might already be registered or there was an issue.");
                    }
                },
                error: function (error) {
                    console.log(error);
                    // Show an error alert in case of issues with the AJAX request
                    alert("An error occurred during the AJAX request.");
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#forgotPass").click(function () {
            // Show the registration popup
            $("#lostPass").modal('show');
        });

        // Handle registration form submission via AJAX
        $("#lostForm").submit(function (e) {
            e.preventDefault(); // Prevent normal form submission

            // Check if passwords match
            var password = $("#lostPass").val();
            var confirmPassword = $("#confirmPassword").val();

            if (password !== confirmPassword) {
                alert("Passwords do not match. Please confirm your password.");
                return;
            }

            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
                data: $(this).serialize(),
                success: function (response) {
                    console.log("Server Response:", response);
                    // Handle the server response
                    if (response === '{"success":true,"message":"success"}') {
                        // Registration successful, show a success alert
                        alert("Password update request successful, Check your email inbox to update your password.");
                        // Close the registration popup
                        $("#lostPass").modal('hide');
                    } else {
                        // Otherwise, show an error alert
                        alert("Error during request. Maybe you've exceeded the try-later request threshold.");
                    }
                },
                error: function (error) {
                    console.log(error);
                    // Show an error alert in case of issues with the AJAX request
                    alert("An error occurred during the request.");
                }
            });
        });
    });
</script>


    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>