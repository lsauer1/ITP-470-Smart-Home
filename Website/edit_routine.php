<?php
require 'config.php';
if (isset($_SESSION['email']) && isset($_GET['id'])) {
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
  if (!($stmt = $mysqli->prepare("SELECT *, routines.id AS routine_id, households_has_users.user_id AS user_id FROM routines JOIN households_has_users ON households_has_users.household_id = routines.household_id WHERE routines.id = ? AND user_id = ?;"))) {
    echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
  }
  $stmt->bind_param("ii", $_GET['id'], $user["user_id"]);
  if (!$stmt->execute()) {
    echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
    exit();
  }
  $routine = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  if (!($stmt = $mysqli->prepare("SELECT * FROM households WHERE id = ?;"))) {
    echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
  }
  $stmt->bind_param("i", $routine['household_id']);
  if (!$stmt->execute()) {
    echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
    exit();
  }
  $household = $stmt->get_result()->fetch_assoc();
  if (!($stmt = $mysqli->prepare("SELECT * FROM devices WHERE household_id = ?;"))) {
    echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
  }
  $stmt->bind_param("i", $routine['household_id']);
  if (!$stmt->execute()) {
    echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
    exit();
  }
  $devices = array();
  $result = $stmt->get_result();
  for ($i = 0; $i < $result->num_rows; $i++) {
    array_push($devices, $result->fetch_assoc());
  }
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  <?php 
  if (isset($routine)) {
    ?>
    <div class="col-12 no-padding">
      <div class="jumbotron jumbotron-fluid">
        <form <?php echo 'action="update_routine.php?id='.$_GET['id'].'"';?> method="post">
          <div class="row justify-content-center align-items-center runover-row">
            <div class="col-12">
              <h1 class="display-4 text-dark jumbotron-text text-center"><?php echo $routine["name"]; ?></h1>
            </div>
          </div>
          <p class="lead text-dark">
            <div class="row justify-content-center align-items-center runover-row">
              <div class="col-12 no-padding">
                <div class="row justify-content-center align-items-center input-row runover-row">
                  <div class="col-5 no-padding input-cols runover-row">
                    <div class="row justify-content-center align-items-center text-center runover-row">
                      <div class="input-group mb-3 flex-nowrap">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Name</span>
                        </div>
                        <input type="text" name="name" class="form-control" required placeholder="Name" aria-label="Device Name" aria-describedby="basic-addon1" value=<?php echo '"'.$routine["name"].'"'; ?>>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row justify-content-center align-items-center input-row runover-row">
                  <div class="col-5 no-padding input-cols runover-row">
                    <div class="row justify-content-center align-items-center text-center runover-row">
                      <div class="input-group mb-3 flex-nowrap">
                        <div class="input-group-prepend">
                          <label class="input-group-text" for="household_id">Household</label>
                        </div>
                        <select disabled class="custom-select" id="household_id" name="household_id">
                          <?php
                          foreach ($household_relations as $houseid) {
                            if ($houseid["household_id"] = $device["household_id"]) {
                              echo '<option selected value="'.$houseid["household_id"].'">'.$houseid["name"].'</option>';
                            } else {
                              echo '<option value="'.$houseid["household_id"].'">'.$houseid["name"].'</option>';
                            }
                          }              
                          ?>
                        </select>
                      </div>
                    </div>

                  </div>
                </div>

                <div class="col-3 no-padding input-cols runover-row">

                </div>
              </div>
            </div>

          </p>
          <div class="row justify-content-center align-items-center runover-row">
            <input class="btn btn-primary" type="submit" value="Submit">
            &nbsp&nbsp
            <a class="btn btn-danger" onclick='return confirm("Are you sure you want to delete this routine?")' href=<?php echo '"delete_routine.php?id='.$_GET['id'].'"'?>>Delete</a>
          </div>
          <br>
          <div class="row justify-content-center align-items-center text-center runover-row">
            <?php
            if ($routine['favorite']) {
              echo '<h3 class="text-center text-dark">Favorite <a  href="favorite_routine.php?id='.$routine["routine_id"].'&favorite=0&return=0" class="text-dark"><i class="fas fa-star"></i></a></h3>'; 
            } else {
              echo '<h3 class="text-center text-dark">Favorite <a href="favorite_routine.php?id='.$routine["routine_id"].'&favorite=1&return=0" class="text-dark"><i class="far fa-star"></i></a></h3>'; 
            }
            ?>
          </div>
        </form>
      </div>
    </div>
    <div class="body">
      <div class="col-12">
        <div class="row justify-content-center align-items-center runover-row">
          <div class="col-12"><h3 class="text-center">New Action:</h3>
          </div>
        </div>
        <div class="row justify-content-center align-items-center runover-row">
          <div class="col-12">
            <form <?php echo 'action="update_routine.php?id='.$routine["routine_id"].'"'; ?> method="post">
              <div class="row justify-content-center align-items-center">
                <div class="col-12">
                  <div class="input-group mb-3 flex-nowrap">
                    <div class="input-group-prepend">

                      <select required class="btn btn-secondary dropdown-toggle" id="aio-command" name="device">
                        <option class="dropdown-item text-white" disabled selected value="">Device</option>
                        <?php
                        foreach ($devices as $device) {
                          echo '<option class="dropdown-item text-white" value="'.$device['device_id'].'">'.$device['name'].'</option>';
                        }
                        ?>
                      </select>
                      <select required class="btn btn-secondary dropdown-toggle" id="aio-command" name="command">
                        <option class="dropdown-item text-white" disabled selected value="">CMD</option>
                        <option class="dropdown-item text-white" value="ON">ON</option>
                        <option class="dropdown-item text-white" value="OFF">OFF</option>
                        <option class="dropdown-item text-white" value="DIM">DIM</option>
                      </select>
                    </div>
                    <input  class="form-control" required type="text" name="value" id="aio-value" placeholder="Value"> 
                    <div class="input-group-append">
                      <input class="btn btn-primary" type="submit" value="Send">
                    </div> 
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="row justify-content-center align-items-center">
          <div class="col-9">
            <div class="row justify-content-center align-items-center">
              <h3 class="text-center">Last Ten Commands:</h3>
            </div>
          </div>
        </div>
        <div class="row justify-content-center align-items-center">
          <div class="col-12">
            <table class="table table-hover table-striped">
              <thead>
                <tr>
                  <th scope="col" class="mobile-remove">Created At</th>
                  <th scope="col" class="mobile-remove">Expires At</th>
                  <th scope="col">Command</th>
                  <th scope="col">Value</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $data = sendCURL('https://io.adafruit.com/api/v2/'.$master_aiouser.'/feeds/'.$device["feed_name"].'/data?limit=10', array(), array('X-AIO-KEY: '.$master_aiokey), "GET");
                foreach ($data as $datum) {
                  echo '<tr>';
                  echo "<td scope='row' class='mobile-remove'>".$datum->created_at."</td>";
                  echo "<td class='mobile-remove'>".$datum->expiration."</td>";
                  echo "<td>".explode(":", $datum->value)[0]."</td>";
                  echo "<td>".explode(":", $datum->value)[1]."</td>";
                  echo '</tr>';
                }
                $mysqli->close();

                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="js/bootstrap.min.js"></script>
  <script src="js/main.js"></script>
  <script src="https://kit.fontawesome.com/aad48dc15b.js" crossorigin="anonymous"></script>
  <script src="https://apis.google.com/js/platform.js"></script>
</body>
</html>