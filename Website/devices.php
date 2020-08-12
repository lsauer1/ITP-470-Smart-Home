<?php 
require 'config.php';
if (isset($_SESSION['email'])) {
  if (!($stmt = $mysqli->prepare("SELECT * FROM users WHERE email=?;"))) {
    echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
  }
  $stmt->bind_param("s", $_SESSION['email']);
  if (!$stmt->execute()) {
    echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
    exit();
  }
  $user = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  if (!($stmt = $mysqli->prepare("SELECT household_id, owner_id, households.name FROM households_has_users JOIN households ON households.id = household_id WHERE user_id=?;"))) {
    echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
  }
  $stmt->bind_param("i", $user['user_id']);
  if (!$stmt->execute()) {
    echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
    exit();
  }
  $household_relations = array();
  $result = $stmt->get_result();
  for ($i = 0; $i < $result->num_rows; $i++) {
    array_push($household_relations, $result->fetch_assoc());
  }
  $stmt->close();
  $devices = array();
  foreach ($household_relations as $houseid) {
    $query = "SELECT *, households.name AS household_name, devices.name AS device_name FROM devices JOIN households ON households.id = devices.household_id WHERE devices.household_id = ?";
    if (!($stmt = $mysqli->prepare($query.';'))) {
      echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
    }
    $stmt->bind_param("i", $houseid['household_id']);
    if (!$stmt->execute()) {
      echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
      exit();
    }
    $result = $stmt->get_result();
    for ($i = 0; $i < $result->num_rows; $i++) {
      array_push($devices, $result->fetch_assoc());
    }
    $favorite = array_column($devices, 'favorite');

    array_multisort($devices, SORT_ASC, $favorite);
    $stmt->close();
  }
  
} else {
  echo '<div class="text-danger font-italic">Missing Parameters</div>';
  exit();
}
$mysqli->close();
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
  <div class="body">
    <div class="row justify-content-center align-items-center runover-row mobile-show">
      <div class="col-12">
        <h3 class="text-center">Create Device:</h3>
      </div>
    </div>
    <div class="row justify-content-center align-items-center runover-row mobile-show">
      <div class="col-12">

        <form action="add_device.php" method="post">
          <div class="row justify-content-center align-items-center mobile-show">
            <div class="col-12">
              <div class="input-group mb-3 flex-nowrap">
                <div class="input-group-prepend household-dropdown">
                  <select required name="household_id" id="household_id" class="btn btn-secondary dropdown-toggle household-dropdown">
                    <option value="" disabled selected>Household</option>
                    <?php 
                    foreach ($household_relations as $houseid) {
                      echo '<option value="'.$houseid["household_id"].'">'.$houseid["name"].'</option>';
                    }
                    ?>
                  </select>
                </div>
                <input required type="text" name="name" placeholder="Device Name" class="form-control">
                <div class="input-group-append">
                  <?php
                  echo '<input style="display:none;" type="text" name="email" value="'.$_SESSION['email'].'" />';
                  ?>
                  <input style="display:none;" type="text" name="feed" placeholder="Feed Name" class="form-control">
                  <input class="btn btn-primary" type="submit" value="Create">
                </div> 
              </div>
            </div>
          </div>
        </form>
      </div>

    </div>

    <div class="row justify-content-center align-items-center runover-row">
      <div class="col-9">
        <div class="row justify-content-center align-items-center runover-row">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th scope="col"><i class="far fa-star"></i></th>
                <th scope="col">Device Name</th>
                <th scope="col" class="mobile-remove">Feed Name</th>
                <th scope="col" class="mobile-remove">Household</th>
                <th scope="col" class='mobile-remove'>Edit/Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($devices as $device) {

                echo '<tr>';
                if ($device["favorite"]) {
                  echo '<td scope="row"><a href="favorite_device.php?id='.$device["device_id"].'&favorite=0&return=1" class="text-white"><i class="fas fa-star"></i></a></td>';
                } else {
                  echo '<td scope="row"><a href="favorite_device.php?id='.$device["device_id"].'&favorite=1&return=1" class="text-white"><i class="far fa-star"></i></a></td>';
                }
                echo '<td><a href="edit_device.php?id='.$device["device_id"].'" class="btn btn-outline-light device-name">'.$device["device_name"]."</a></td>";
                echo "<td class='mobile-remove'><span class='nav-link text-white'>".$device["feed_name"]."</span></td>";
                echo "<td class='mobile-remove'><span class='nav-link text-white'>".$device["household_name"]."</span></td>";
                echo '<td class="mobile-remove"><div class="row"><a class="btn btn-primary spacing-right" href="edit_device.php?id='.$device["device_id"].'"><i class="fa fa-edit" aria-hidden="true"></i></a><a class="btn btn-danger" onclick="return confirm("Are you sure you want to delete this device?")" href="delete_device.php?id='.$device["device_id"].'&feed='.$device["feed_name"].'"><i class="fa fa-trash" aria-hidden="true"></i></a></div></td>';
              }
              ?>
              <tr class="mobile-remove">
                <form action="add_device.php" method="post">
                  <?php
                  echo '<input style="display:none;" type="text" name="email" value="'.$_SESSION['email'].'" />';
                  ?>
                  <td scope="row"><h5>Add Device:</h5></td>
                  <td><input required type="text" name="name" placeholder="Device Name" class="form-control"></td>
                  <td><input type="text" name="feed" placeholder="Feed Name" class="form-control"></td>
                  <td>
                    <select name="household_id" id="household_id" class="form-control">
                      <option value="" disabled selected>Select Household</option>

                      <!-- Rating dropdown options here -->
                      <?php 
                      foreach ($household_relations as $houseid) {
                        echo '<option value="'.$houseid["household_id"].'">'.$houseid["name"].'</option>';
                      }
                      ?>
                    </select>
                  </td>
                  <td><input type="submit" value="Create Device" class="btn btn-primary"></td>
                </form>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
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