<?php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="google-signin-client_id" content="1076189564255-qov3gglu390cab0ovg7a82msegai63fk.apps.googleusercontent.com">
  <title>Smart Home System</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/custom-style.css">
</head>
<body onload="if (document.forms['user-form'] != null) {document.forms['user-form'].submit();}">
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
  <div class="body">
    <?php 
    if (!(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['id']) && isset($_POST['avatar-url']))) {
      header("Location: index.php");
    }
    $query = "SELECT * FROM users WHERE google_id=".$_POST['id'].";";
    $user = $mysqli->query($query);
    if(!$user) {
      echo $mysqli->error;
      exit();
    }

    if ($user->num_rows == 0) {
      echo '<div style="text-align: center;">';
      echo "<h1>Welcome New User!</h1>";
      echo "<hr>";
      echo "<h3>Please make sure all the following information is correct!</h3>";
    //echo '<div class="col-12">';
      echo '<form action="add_user.php" method="post">';
      echo '<div class="row justify-content-center align-items-center runover-row">';

      echo '<div class="col-9">';
      echo '<div class="input-group mb-3 flex-nowrap"><div class="input-group-prepend"><span class="input-group-text">Name:</span>';
      echo '</div>';
      echo '<input type="text" name="name" class="form-control" value="'.$_POST['name'].'">';                
      echo '</div></div></div>';
      echo '<div class="row justify-content-center align-items-center runover-row">';

      echo '<div class="col-9">';
      echo '<div class="input-group mb-3 flex-nowrap"><div class="input-group-prepend"><span class="input-group-text">Email:</span>';
      echo '</div>';
      echo '<input readonly="readonly" type="email" name="email" class="form-control" value="'.$_POST['email'].'">';
      echo '</div>';

      echo '</div></div>';
      echo '<div class="row justify-content-center align-items-center runover-row">';
      echo '<div class="col-9">';    
      echo '<div class="input-group mb-3 flex-nowrap"><div class="input-group-prepend"><span class="input-group-text">Google ID:</span>';
      echo '</div>';
      echo '<input readonly="readonly" type="text" name="id" class="form-control" value="'.$_POST['id'].'">';
      echo '</div></div></div>';
      echo '<div class="row justify-content-center align-items-center runover-row">';
      echo '<div class="col-9">';
      echo '<div class="input-group mb-3 flex-nowrap text-center"><div class="input-group-prepend"><span class="input-group-text">Avatar URL:</span>';
      echo '</div>';
      echo '<input type="url" name="avatar_url" class="form-control" value="'.$_POST['avatar-url'].'">';
      echo '</div>';
      echo '</div>';
      echo '<br>';
      echo '</div>';
      echo '<div class="row justify-content-center align-items-center runover-row">';
      echo '<div class="col-9"><input type="submit" class="btn btn-primary" value="Create New User"></div></div>';
      echo '</form>';

      echo '</div>';

    } else {
      $current_user = $user->fetch_assoc();
      $_SESSION["email"] = $current_user['email'];
      $_SESSION['id'] = $current_user['google_id'];
      echo '<script> sessionStorage.setItem("email", "' . $current_user['email'] . '");</script>';
      echo '<script> sessionStorage.setItem("id", "' . $current_user['google_id'] . '");</script>';
      echo '<form action="user.php" name="user-form" method="post"> <input style="display:none;" type="text" name="email" value="'.$current_user['email'].'" /> <input style="display:none;" type="text" name="google_id" value="'.$current_user['google_id'].'" /></form>';
      exit();
    }
    $mysqli->close();
    ?>
  </div>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
  <script src="https://kit.fontawesome.com/aad48dc15b.js" crossorigin="anonymous"></script>
  <script src="https://apis.google.com/js/platform.js"></script>
</body>
</html>