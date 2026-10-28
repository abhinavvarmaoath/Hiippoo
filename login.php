<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
session_start();
date_default_timezone_set("America/Los_Angeles");
$time = date('Y/m/d g:i:s A');
$conn = db_connect();
$row = false;
if(isset($_POST['username']))
{
	csrf_verify();
	$username=$_POST['username'];
	$password=$_POST['password'];
	$stmt = $conn->prepare("SELECT * FROM enc WHERE username=? LIMIT 1");
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$res = $stmt->get_result();
	$row = $res->fetch_assoc();
	if ($row && !password_verify($password, $row['password'])) {
	    $row = false;
	}
}
	if($row)
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
header('Location: signup_form.php');
}
?>
