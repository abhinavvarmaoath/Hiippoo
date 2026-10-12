<?php
require_once __DIR__ . '/db.php';
session_start();
date_default_timezone_set("America/Los_Angeles");
$time = date('Y/m/d g:i:s A');
$conn = db_connect();
$res = false;
if(isset($_POST['username']))
{
	$username=$_POST['username'];
	$password=$_POST['password'];
        $password = md5($password);
	$stmt = $conn->prepare("SELECT * FROM enc WHERE username=? AND password=? LIMIT 1");
	$stmt->bind_param('ss', $username, $password);
	$stmt->execute();
	$res = $stmt->get_result();
}
	if($res && $res->num_rows==1)
 {
// Set username session variable
$_SESSION['username'] = $username;

$stmt = $conn->prepare("INSERT INTO log (username,time) VALUES (?,?)");
       $stmt->bind_param('ss', $username, $time);
       $data = $stmt->execute() or die($conn->error);

 header('Location: home1.php');
}
else
{
// Jump to login page
header('Location: signup.html');
}
?>
