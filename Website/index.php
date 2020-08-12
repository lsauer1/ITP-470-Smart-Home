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
    $query = "SELECT *, households.name AS household_name, devices.name AS device_name FROM devices JOIN households ON households.id = devices.household_id WHERE devices.household_id = ? AND devices.favorite = 1";
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

    $stmt->close();
  }
  $mysqli->close();
}
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
      <a class="navbar-brand text-white active" href="index.php">Smart Home</a>
      <button class="navbar-toggler" id="dropdown" type="button" data-toggle="collapse" data-target="#navbar-toggler" aria-controls="navbar-toggler" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars text-white"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbar-toggler">
        <div class="navbar-nav mr-auto">
          <a class="nav-item nav-link text-white" id="house-link" href="household.php">Households</a>
          <a class="nav-item nav-link text-white" id="device-link" href="devices.php">Devices</a>
          <a class="nav-item nav-link text-white" id="routine-link" href="routines.php">Routines</a>
        </div>
        <div class="navbar-nav ml-auto">
          <form action="user.php" method="post" id="myForm">
            <input style="display:none;" type="text" name="email" id="user_email" />
            <input style="display:none;"  type="text" name="google_id" id="user_id"/>
            <a href="#" id="submit_user" class="nav-item nav-link text-white" onclick="document.getElementById('myForm').submit();">User</a>
          </form>
          <div class="nav-item g-signin2" data-onsuccess="onSignIn" data-theme="dark">Login</div>
          <a class="nav-item nav-link text-white" id="signout" href="#">Logout</a>
        </div>
      </div>
    </div>
  </nav>
  <?php if (isset($user)) { ?>
    <div class="jumbotron jumbotron-fluid">
      <h1 class="display-4 text-dark text-center">Welcome <?php echo $user['name']; ?>!</h1>
      <p class="lead text-dark text-center">Please explore the website. Its primary purpose is to control smart home devices I am building for an ITP 470 class!</p>
      <hr class="my-4">
    </div>
  <?php } else { ?>
    <div class="jumbotron jumbotron-fluid">
      <h1 class="display-4 text-dark text-center">Welcome New User!</h1>
      <p class="lead text-dark text-center">Please explore the website. Its primary purpose is to control smart home devices I am building for an ITP 470 class! Once you make an account by linking a gmail, you will be all set!</p>
      <hr class="my-4">
    </div>
  <?php } ?>

  <div class="body">
    <h1 class="text-center">Favorite Devices: <a href="devices.php" class="btn btn-secondary">To Device Page</a></h1>
    <?php if (count($devices) > 0) { ?>

      <div id="deviceIndicators" class="carousel slide" data-interval="false">
        <ol class="carousel-indicators">
          <?php
          for ($i = 0; $i < count($devices); $i++) {
            if ($i == 0) {
              echo '<li data-target="#deviceIndicators" data-slide-to="0" class="active"></li>';
            } else {
              echo '<li data-target="#deviceIndicators" data-slide-to="'.$i.'"></li>';
            }
          }
          ?>
        </ol>
        <div class="carousel-inner">
          <?php
          for ($i = 0; $i < count($devices); $i++) {
            if ($i == 0) {
              echo '<div class="carousel-item active">';
            } else {
              echo '<div class="carousel-item">';
            }
            ?>
            <div class="row justify-content-center align-items-center">
              <div class="col-9">
                <div class="card" >
                  <?php echo '<div class="card-header text-center"><h2 class="text-center">'.$devices[$i]['device_name'].'</h2></div>'; ?>
                  <div class="card-body">
                    <?php echo '<h5 class="card-title text-center">See the Last Command or Send a New One:</h5>';
                    echo '<p class="card-text"><form id="command-form" name="command-form" action="https://io.adafruit.com/api/v2/'.$master_aiouser.'/feeds/'.$devices[$i]["feed_name"].'/data" method="post">'; ?>
                    <div class="row justify-content-center align-items-center mobile-show">
                      <div class="col-12">
                        <?php
              $data = sendCURL('https://io.adafruit.com/api/v2/'.$master_aiouser.'/feeds/'.$devices[$i]["feed_name"].'/data?limit=1', array(), array('X-AIO-KEY: '.$master_aiokey), "GET")[0];
                        ?>
                        <div class="input-group mb-3 flex-nowrap">
                          <div class="input-group-prepend">
                            <select required class="btn btn-secondary dropdown-toggle" id="aio-command" name="command">
                              <option class="dropdown-item" disabled selected value="">CMD</option>
                              <?php 
                              if (explode(":", $data->value)[0] == "ON") {
                                echo '<option class="dropdown-item" selected value="ON">ON</option>';
                              } else {
                                echo '<option class="dropdown-item"  value="ON">ON</option>';
                              }
                              if (explode(":", $data->value)[0] == "OFF") {
                                echo '<option class="dropdown-item" selected value="OFF">OFF</option>';
                              } else {
                                echo '<option class="dropdown-item"  value="OFF">OFF</option>';
                              }
                              if (explode(":", $data->value)[0] == "DIM") {
                                echo '<option class="dropdown-item" selected value="DIM">DIM</option>';
                              } else {
                                echo '<option class="dropdown-item"  value="DIM">DIM</option>';
                              }
                              ?>
                            </select>
                          </div>
                          <input <?php echo 'value="'.explode(":", $data->value)[1].'"'; ?>  class="form-control" required type="text" name="value" id="aio-value" placeholder="Value"> 
                          <div class="input-group-append">
                            <input class="btn btn-primary" type="submit" value="Send">
                          </div> 
                        </div>

                      </div>
                    </div>
                  </form></p>
                  <?php echo '<div class="text-center"><a href="edit_device.php?id='.$devices[$i]['device_id'].'" class="btn btn-primary ">Go to Device Page</a></div>'
                  ; ?>
                  <hr>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php }?>
    </div>
    <a class="carousel-control-prev" href="#deviceIndicators" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#deviceIndicators" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
<?php } else { ?>
  <div class="row justify-content-center align-items-center">
    <h5 class="text-center">Favorite some devices to see them here!</h5>
  </div>
<?php } ?>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="https://kit.fontawesome.com/aad48dc15b.js" crossorigin="anonymous"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
</body>
</html>