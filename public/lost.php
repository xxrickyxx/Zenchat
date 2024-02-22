<?php  

session_start();
require_once 'conn/index.php';

$token=$_GET['token']; 
$stato=1;
$username=$_GET['username']; 

if (!!$_GET['username']&& !!$_GET['token']) {

$sql = "SELECT * FROM lostpass WHERE token = :token and stato = :stato";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':token', $token, PDO::PARAM_STR); // :username è il placeholder per il parametro
$stmt->bindParam(':stato', $stato, PDO::PARAM_STR);
$stmt->execute();
$verifica_token = $stmt->fetch();
 // Verifica la validità del token e il tempo di scadenza
 $tokenCreationTime = strtotime($verifica_token['data']); // Assumendo che la colonna del timestamp si chiami 'timestamp'
 $maxTokenAge = 6 * 60 * 60; // 6 ore in secondi

if (!!$verifica_token && time() - $tokenCreationTime <= $maxTokenAge) {


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

    <title>Lost Password</title>
  </head>
  <body>

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('images/bg_1.jpg');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
             <span class="ml-auto"><a href="https://zenchat.it" target="_blank" class="forgot-pass">Home</a>
                <a href="https://kloe.zenchat.it" class="forgot-pass">Login</a>
             </span> 
<div class="mb-4">
    <h3>Update Password <img style="width:70px; border-radius:10px 10px 10px;" src="images/kloe.gif" alt="homepage" class="dark-logo" /></h3>
</div>
            <form id="updatePass">
              <div class="form-group first">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password">

              </div>
              <div class="form-group last mb-3">
                <label for="password2">Password</label>
                <input type="password" class="form-control" id="password2" name="password2">
                
              </div>
              <input type="hidden" name="username" value="<?php echo $username; ?>">
              <input type="hidden" name="token" value="<?php echo $token; ?>">
              <input type="submit" value="Update" class="btn btn-block btn-primary">

            </form>
          </div>
        </div>
      </div>
    </div>

    
  </div>
    

    <script src="js/jquery-3.3.1.min.js"></script>

<!-- Aggiungi questo script jQuery alla fine del tuo HTML -->

<script>
  $(document).ready(function () {
    $("#updatePass").submit(function (event) {
      // Prevent the default form submission
      event.preventDefault();

      // Get password values
      var password = $("#password").val();
      var password2 = $("#password2").val();

      // Check if passwords match
      if (password !== password2) {
        // Passwords do not match, show an alert
        alert("Passwords do not match. Please re-enter your passwords.");
        return; // Stop form submission
      }

      // If passwords match, proceed with the AJAX request
      var formData = $(this).serialize();

      $.ajax({
        type: "POST",
        url: "https://kloe.zenchat.it/ajax/updatePass.php",
        data: formData,
        success: function (response) {
          // Handle the response from the server
          console.log(response);

          // Check for "success token" and "success user" in the response
          if (response.includes("success token") && response.includes("success user")) {
            // Both conditions are met, show an alert
            alert("Password updated successfully!");

                        // Redirect to the specified URL
            window.location.href = "https://kloe.zenchat.it";
          } else {
            // One or both conditions are not met, handle accordingly
            alert("Something went wrong. Please try again.");
          }
        },
        error: function (error) {
          // Handle errors
          console.error("Error sending AJAX request:", error);
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




<?php   

} 

   header("Location: https://kloe.zenchat.it/error");

} else {

   header("Location: https://kloe.zenchat.it/error");


}

?>