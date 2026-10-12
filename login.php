<?php
require_once __DIR__ . '/db.php';
session_start();
$_SESSION['username'] = $_POST['username'];
date_default_timezone_set("America/Los_Angeles");
$time = date('Y/m/d g:i:s A');
$conn = db_connect();
$res = false;
if(isset($_POST['username']))
{
	$username=$_POST['username'];
	$password=$_POST['password'];
        $password = md5($password);
	$sql="SELECT * FROM enc WHERE username='".$username."' AND password='".$password."' LIMIT 1";
	$res=$conn->query($sql);
}
	if($res && $res->num_rows==1)
 {
// Set username session variable

$query = "INSERT INTO log (username,time) VALUES ('$username','$time')";
       $data = $conn->query($query) or die($conn->error);

 header('Location: home1.php');
}
else
{
// Jump to login page
header('Location: signup.html');
}
?>
