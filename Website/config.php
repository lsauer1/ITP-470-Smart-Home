<?php
session_start();
$host = "sql3.freemysqlhosting.net";
$user = "sql3360040";
$password = "q7hnAiFxzK";
$db = $user;
$master_aiouser = "ls1";
$master_aiokey = "aio_AgFi49NCd2SOAbUlh8LiCrbjhbAG";
$mysqli = new mysqli($host, $user, $password, $db);
$aiouser = $master_aiouser;
$aiokey = $master_aiokey;

if ($mysqli->connect_errno) {
	echo $mysqli->connect_error;
	exit();
}
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
	$aiouser = $user['adafruit_io_user'];
	$aiokey = $user['adafruit_io_key'];
}


function sendCURL($url, $data, $headers, $request) {
	$data_string = json_encode($data);
	array_push($headers, 'Content-Length: ' . strlen($data_string));
	array_push($headers, 'Content-Type: application/json');
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result);
}
?>