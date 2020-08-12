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
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
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
  <div class="body">
    <?php 
    if (isset($_SESSION["id"]) && (isset($_POST['id']) || isset($_GET['id']))) {
      if (!($stmt = $mysqli->prepare("SELECT * FROM users WHERE google_id=?;"))) {
        echo '<div class="text-danger font-italic">(' . $mysqli->errno . ") " . $mysqli->error.'</div>';
      }
      $stmt->bind_param("s", $_SESSION["id"]);
      if (!$stmt->execute()) {
        echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
        exit();
      }
      $user = $stmt->get_result()->fetch_assoc();
      $stmt->close();
      $stmt = $mysqli->prepare("SELECT * FROM devices WHERE devices.household_id=?;");
      if (!$stmt->bind_param("i", $_POST['id'])) {
        echo "(".$stmt->errno.") ".$stmt->error;
      }
      if (!$stmt->execute()) {
        echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
        exit();
      }
      $devices = array();
      $result = $stmt->get_result();
      for ($i = 0; $i < $result->num_rows; $i++) {
        array_push($devices, $result->fetch_assoc());
      }
      foreach ($devices as $device) {
        sendCURL('https://io.adafruit.com/api/v2/'.$master_aiouser.'/feeds/'.$device['feed_name'], array(), array('X-AIO-KEY: '.$master_aiokey), "DELETE");
      }
      $stmt = $mysqli->prepare("DELETE FROM devices WHERE household_id=?");
      if (isset($_POST['id'])) {
        $id=$_POST['id'];
      } else {
        $id = $_GET['id'];
      }
      if (!$stmt->bind_param("i", $id)) {
        echo "(".$stmt->errno.") ".$stmt->error;
      }
      if (!$stmt->execute()) {
        echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
        exit();
      } 
      $stmt->close();
      $stmt = $mysqli->prepare("DELETE FROM households_has_users WHERE household_id=? AND user_id=?;");
      if (!$stmt->bind_param("ii", $id, $user['user_id'])) {
        echo "(".$stmt->errno.") ".$stmt->error;
      }

      if (!$stmt->execute()) {
        echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
        exit();
      } 
      $stmt->close();
      $stmt = $mysqli->prepare("DELETE FROM households WHERE id=? AND owner_id=?;");
      if (!$stmt->bind_param("ii", $id, $user['user_id'])) {
        echo "(".$stmt->errno.") ".$stmt->error;
      }

      if (!$stmt->execute()) {
        echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
        exit();
      } else {
        echo '<div class="text-success">Household was successfully deleted.</div>';
        header("Location: household.php");
      }
      $stmt->close();
    } else {
      echo '<div class="text-danger font-italic">Missing Parameters</div>';
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