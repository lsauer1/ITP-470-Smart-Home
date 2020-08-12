<?php
require 'config.php';
if (!($stmt = $mysqli->prepare("SELECT * FROM users WHERE email=?;"))) {
  echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
}
$stmt->bind_param("s", $_SESSION['email']);
if (!$stmt->execute()) {
  echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
  exit();
}
$current_user = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!($stmt = $mysqli->prepare("SELECT * FROM households_has_users WHERE user_id=?;"))) {
  echo '<div class="text-danger font-italic">('.$mysqli->errno.") " . $mysqli->error.'</div>';
}
$stmt->bind_param("i", $current_user['user_id']);
if (!$stmt->execute()) {
  echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
  exit();
}
$household_ids = array();
$result = $stmt->get_result();
for ($i=0; $i<$result->num_rows; $i++) {
  array_push($household_ids, $result->fetch_assoc()["household_id"]);
}
$stmt->close();
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
  if (!isset($_POST['id']) && !isset($_GET['id'])) {
    $households = array();
    foreach ($household_ids as $id) {
      $query = "SELECT * FROM households WHERE id = ?;";
      if (!($stmt = $mysqli->prepare($query))) {
        echo '<div class="text-danger font-italic">('.$mysqli->errno.") ".$mysqli->error.'</div>';
      }
      $stmt->bind_param("i", $id);
      if (!$stmt->execute()) {
        echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
        exit();
      }   
      $result = $stmt->get_result();
      array_push($households, $result->fetch_assoc());
      $stmt->close();
    }
    echo '<div class="body">';
    echo '<div class="row justify-content-center align-items-center runover-row">';
    echo '<h1 class="text-center">Select a Household!</h1>';
    echo '</div>';
    echo '<div class="row justify-content-center align-items-center  runover-row">';
    echo '<div class="col-12"><form action="add_household.php" method="post">';
    echo '<table class="table table-hover">';
    echo '<thead><tr><th scope="col">Name</th><th scope="col" class="mobile-remove">Permission</th><th scope="col">Delete</th></tr></thead><tbody>';
    foreach($households as $household) {
      echo '<tr><td scope="row"><a class="btn btn-outline-light" href="household.php?id='.$household["id"].'&email='.$_GET['email'].'">'.$household['name'].'</a></td>';
      if ($household["owner_id"] == $current_user["user_id"]) {
        echo "<td class='mobile-remove'><span class='nav-link text-white mobile-remove'>Owner</span></td>";
      } else {
        echo "<td class='mobile-remove'><span class='nav-link text-white mobile-remove'>Member</span></td>";
      }
      if ($household['owner_id'] == $current_user['user_id']) {
        echo '<td><form action="delete_household.php" method="post">';
        echo '<input style="display:none;" type="text" name="email" value="'.$_GET['email'].'" /> <input style="display:none;" type="text" name="id" value="'.$household["id"].'" />';
        echo '<button class="btn btn-danger text-white delete-house" type="submit" onclick="return confirm(\'Are you sure you want to delete your household?\');">Delete Household</button>';
        echo '</form></td></tr>';    
      } else {
        echo '<td><button class="btn btn-secondary delete-house" disabled>Delete Household</button></td></tr>';
      }
    }

    echo '<tr class="mobile-remove">';
    echo '<td class="mobile-remove"><h5 class="text-center">Create New Household:</h5></td>';
    echo '<td><input type="text" name="name1" placeholder="Household Name" class="form-control"></td>';
    echo '<td><input type="submit" class="btn btn-primary" value="Create Household"></td>';

    echo '</tr>  </tbody> </table>';
    echo '<div class="row justify-content-center align-items-center mobile-show">';
    echo '<div class="input-group mb-3 flex-nowrap">';

    echo '<input type="text" name="name2" placeholder="New Household Name" class="form-control">';
    echo '<div class="input-group-append"><input type="submit" class="btn btn-primary" value="Create"></div></div><br>';
    echo '</div></form></div></div></div>';
    
  }else {
    $query = "SELECT *, users.user_id AS user_id, users.name AS user_name, users.email AS email, households.owner_id AS owner_id, households.name AS household_name FROM households_has_users JOIN users On users.user_id = households_has_users.user_id JOIN households ON households.id = household_id WHERE households_has_users.household_id = ?;";
    if (!($stmt = $mysqli->prepare($query))) {
      echo '<div class="text-danger font-italic">('.$mysqli->errno.") ".$mysqli->error.'</div>';
    }
    $stmt->bind_param("i", $_GET["id"]);
    if (!$stmt->execute()) {
      echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
      exit();
    }   
    $result = $stmt->get_result();
    $users = array();
    for ($i = 0; $i < $result->num_rows; $i++) {
      array_push($users, $result->fetch_assoc());
    } 
    $stmt->close();
    $query = "SELECT * FROM households WHERE id = ?;";
    if (!($stmt = $mysqli->prepare($query))) {
      echo '<div class="text-danger font-italic">('.$mysqli->errno.") ".$mysqli->error.'</div>';
    }
    $stmt->bind_param("i", $_GET["id"]);
    if (!$stmt->execute()) {
      echo '<div class="text-danger font-italic">'.$stmt->error.'</div>';
      exit();
    }   
    $household = $stmt->get_result()->fetch_assoc();   
    ?>
    <div class="jumbotron jumbotron-fluid">
      <?php
      echo '<form action="edit_household.php?id='.$_GET['id'].'" method="POST">';
      ?>
      <div class="row justify-content-center align-items-center">
        <div class="col-12">
          <h1 class="display-4 text-dark jumbotron-text text-center">Selected Household:</h1>
        </div>

      </div>
      <div class="row justify-content-center align-items-center">
        <div class="col-12">
          <h1 class="display-4 text-dark jumbotron-text text-center">        <?php echo $users[0]["household_name"]; ?></h1>
        </div>
      </div>
      <p class="lead text-dark">
        <div class="row justify-content-center align-items-center">
          <div class="col-9">
            <div class="row justify-content-center align-items-center">
              <div class="col-9">
                <div class="input-group mb-3 flex-nowrap">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Name</span>
                  </div>
                  <input type="text" class="form-control" required placeholder="Household Name" name="name" aria-label="Household Name" aria-describedby="basic-addon1" value=<?php echo '"'.$users[0]["household_name"].'"'; ?>>
                  <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit"><i class="fa fa-edit"></i></button>
                    <?php
                    if ($household['owner_id'] == $current_user['user_id']) {
                      echo '<a onclick="return confirm("Are you sure you want to delete this household?");" href="delete_household.php?id='.$_GET['id'].'" class="btn btn-outline-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    }
                    ?>
                  </div>              </div></div>

                </div>
              </div>
            </div>
          </div>
        </p>
      </form>
    </div>
    <div class="body">
      <div class="row justify-content-center align-items-center">
        <h3>Users:</h3>
      </div>
      <div class="row justify-content-center align-items-center">
        <div class="col-9">
          <table class="table table-hover table-striped">
            <thead>
              <tr>
                <th scope="col" class="mobile-remove">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Remove</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($users as $person) {
                echo '<tr>';
                echo '<td scope="col" class="mobile-remove"><span class="nav-link text-white">'.$person['user_name'].'</span></td>';
                echo '<td><span class="small text-white">'.$person['email'].'</span></td>';
                if ($person['owner_id'] == $person['user_id']) {
                  echo '<td><button href="" disabled class="btn btn-secondary">Owner</button></td>';
                } else {
                  if ($person['email'] != $current_user['email']) {
                    echo '<td><a onclick="return confirm("Are you sure you want to remove this user?");" href="remove_user.php?id='.$person['user_id'].'&house='.$_GET["id"].'" class="btn btn-danger">Remove</a></td>';
                  } else {
                    echo '<td><button disabled class="btn btn-secondary">Remove</button></td>';
                  }
                }
                echo '</tr>';
              }
              if ($person['owner_id'] == $current_user['user_id']) {
                echo '<form action="invite_user.php?id='.$_GET['id'].'" method="post"><tr>';
                echo '<td scope="col" class="mobile-remove"><label for="email" required class="col-form-label text-sm-right user-label"><h5>Invite User:</h5></label></td>';
                echo '<td><input type="email" name="email" placeholder="Invite Email" required class="form-control"></td>';
                echo '<td><input type="submit" class="btn btn-primary" value="Invite"></td></tr></form>';
              }
              ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>


  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="js/bootstrap.min.js"></script>
  <script src="https://kit.fontawesome.com/aad48dc15b.js" crossorigin="anonymous"></script>
  <script src="https://apis.google.com/js/platform.js"></script>
  <script src="js/main.js"></script>
</body>
</html>