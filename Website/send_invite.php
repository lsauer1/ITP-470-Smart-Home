
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="google-signin-client_id" content="1076189564255-qov3gglu390cab0ovg7a82msegai63fk.apps.googleusercontent.com">
  <title>Smart Home System</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/custom-style.css">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
  <nav class="navbar navbar-expand-sm navbar-light navbar-custom ">
    <div class="container-fluid"> 
      <a class="navbar-brand text-white" href="index.php">Smart Home</a>
      <button class="navbar-toggler" id="dropdown" type="button" data-toggle="collapse" data-target="#navbar-toggler" aria-controls="navbar-toggler" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars text-white"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbar-toggler">
        <div class="navbar-nav mr-auto">
          <a class="nav-item nav-link text-white active" id="house-link" href="household.php">Households</a>
          <a class="nav-item nav-link text-white" id="device-link" href="devices.php">Devices</a>
          <a class="nav-item nav-link text-white" id="routine-link" href="routines.php">Routines</a>
        </div>
        <div class="navbar-nav ml-auto">
          <form action="user.php" method="post" id="myForm">
            <input style="display:none;" type="text" name="email" id="user_email" />
            <input style="display:none;"  type="text" name="google_id" id="user_id"/>
            <a href="#" id="submit_user" class="nav-item nav-link text-white" onclick="document.getElementById('myForm').submit();">User</a>
          </form>
          <div class="nav-item g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
          <a class="nav-item nav-link text-white" id="signout" href="#">Logout</a>
        </div>
      </div>
    </div>
  </nav>
  <?php
  if (isset($_GET['email'])) {
    $subject = 'Smart Home Invite';

    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $message = '<p>A user has invited you to join my smart home website! <a class="btn btn-primary" href="http://303.itpwebdev.com/~sauer/Final%20Project">Sign Up!</a></p>';
    if (mail($_GET['email'], $subject, $message, $headers)) {
      ?>
      <div class="jumbotron jumbotron-fluid">
        <div class="body text-center">
          <h1 class="display-4 text-dark">Invite Email Sent!</h1>

          <p class="lead text-dark">You can re-invite them to your household after they have registered.</p>
          <hr class="my-4">
          <p class="text-dark">Return to the homepage?</p>
          <p class="lead text-dark">
            <a class="btn btn-primary btn-lg" href="index.php" role="button">Home</a>
          </p>
        </div>
      </div>
    <?php } else {
      ?>
      <div class="jumbotron jumbotron-fluid">
        <div class="body text-center">
          <h1 class="display-4 text-dark">Invite Email Could Not Be Sent!</h1>

          <p class="lead text-dark">Perhaps the email was wron, please try again.</p>
          <hr class="my-4">
          <p class="text-dark">Return to the homepage?</p>
          <p class="lead text-dark">
            <a class="btn btn-primary btn-lg" href="index.php" role="button">Home</a>
          </p>
        </div>
      </div>
      <?php
    }} else header("Location: index.php"); ?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="https://kit.fontawesome.com/aad48dc15b.js" crossorigin="anonymous"></script>
    <script src="https://apis.google.com/js/platform.js"></script>

  </body>
  </html>