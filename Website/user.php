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
  <form action="edit_user.php" method="post">
    <?php 
    if (isset($_SESSION['id']) && isset($_SESSION['email'])) {

      if (!($stmt = $mysqli->prepare("SELECT * FROM users WHERE google_id=? AND email=?"))) {
        echo '<div class="text-danger font-italic">(' . $mysqli->errno . ") " . $mysqli->error.'</div>';
      }
      $stmt->bind_param("is", $_SESSION['id'], $_SESSION['email']);
      if (!$stmt->execute()) {
        echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
        exit();
      }
      $result = $stmt->get_result()->fetch_assoc();
      $stmt->close();
    } else {
      echo '<div class="text-danger font-italic">Missing Parameters</div>';
      exit();
    }
    $mysqli->close();
    echo '<div class="col-12 no-padding">';
    echo '<style> .jumbotron-user {background-image: linear-gradient(rgba(106, 104, 122, 0.75), rgba(106, 104, 122, 0.75)), url("'.$result['avatar_url'].'"); } </style>';
    echo '<div class="jumbotron jumbotron-fluid jumbotron-user">';
    ?>
    <div class="row justify-content-center align-items-center runover-row"> 
      <div class="body col-12">
        <div class="row">
          <div class="col-12">
            <div class="row justify-content-center align-items-center runover-row"> 
              <div class="col-9"><h1 class="display-4 text-white jumbotron-text text-center">Welcome <?php echo $result['name'] ?>!</h1></div>
            </div>
            <br><br>
            <div class="row justify-content-center align-items-center text-center runover-row">
              <div class="col-12">                            
                <div class="input-group mb-3 flex-nowrap">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Name</span>
                  </div>
                  <input required type="text" class="form-control" name="name" placeholder="Name" value=<?php echo '"'.$result['name'].'"' ?>>
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fa fa-edit"></i></button>
                    <?php
                    if ($household['owner_id'] == $current_user['user_id']) {
                      echo '<a onclick="return confirm(\'Are you sure you want to delete this user?\');" href="delete_user.php?id='.$result['user_id'].'" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="body">
      <div class="row justify-content-center align-items-center runover-row non-mobile-row">
        <div class="col-9">
          <div class="mobile-row">
            <div class="col-12">
              <h3 class="text-center">Basic Account Info</h3>
            </div>
          </div>
        </div>
      </div>
  <div class="row justify-content-center align-items-center runover-row non-mobile-row">

    <div class="mobile-row">
      <div class="col-12">
        <div class="input-group mb-3 flex-nowrap">
          <div class="input-group-prepend">
            <span class="input-group-text">Email Address</span>
          </div>
          <input required readonly="readonly" type="email" class="form-control" name="email" placeholder="Email" value=<?php echo '"'.$result['email'].'"' ?>>
        </div>
      </div>
    </div>
      <div class="mobile-row">
        <div class="col-12">
          <div class="input-group mb-3 flex-nowrap">
            <div class="input-group-prepend">
              <span class="input-group-text">Avatar URL</span>
            </div>
            <input required type="text" class="form-control" name="avatar" placeholder="Avatar URL" value=<?php echo '"'.$result['avatar_url'].'"' ?>>
          </div>

        </div>
      </div>
    </div>
      <div class="row justify-content-center align-items-center runover-row non-mobile-row">
        <div class="col-9">
          <div class="mobile-row">
            <div class="col-12">
              <h3 class="text-center">Sign up for an AdafruitIO account here: <a class="btn btn-primary" href="https://accounts.adafruit.com/users/sign_up"> (coming soon)</a></h3>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-center align-items-center runover-row non-mobile-row">

        <div class="mobile-row">
          <div class="col-12">
            <div class="input-group mb-3 flex-nowrap">
              <div class="input-group-prepend">
                <span class="input-group-text">Adafruit Key</span>
              </div>
              <input readonly="readonly" type="text" class="form-control" name="adafruit_key" placeholder="Adafruit IO Key" value=<?php echo '"'.$result['adafruit_io_key'].'"' ?>>      </div>
            </div>
          </div>
          <div class="mobile-row">
            <div class="col-12">
              <div class="input-group mb-3 flex-nowrap">
                <div class="input-group-prepend">
                  <span class="input-group-text">Adafruit Username</span>
                </div>
                <input readonly="readonly" type="text" class="form-control" name="adafruit_user" placeholder="Adafruit IO Username" value=<?php echo '"'.$result['adafruit_io_user'].'"' ?>>      </div>
              </div>

            </div>
          </div>
          <input required type="text" class="form-control user-label" name="id" style="display:none;" value=<?php echo '"'.$result['user_id'].'"' ?>>
      </div>
            </div>
    </form>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="https://kit.fontawesome.com/aad48dc15b.js" crossorigin="anonymous"></script>
    <script src="https://apis.google.com/js/platform.js"></script>
  </body>
  </html>