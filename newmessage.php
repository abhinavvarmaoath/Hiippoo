<?php
require_once __DIR__ . '/db.php';
session_start();
$username = $_SESSION['username'];
$team = $_SESSION['team'];
if(isset($_POST['message']))
{
	$link = db_connect();
	$message = $_POST['message'];
	$username = $_SESSION['username'];
$stmt = $link->prepare("insert into messages (message, username, team) values(?, ?, ?)");
$stmt->bind_param('sss', $message, $username, $team);
$result = $stmt->execute();
mysqli_close($link);
}
echo'<html>';
echo'<head></head><body>';
echo'<form action="newmessage.php" method="post">';
echo'<input type="text" name="message" size="100"/>';
echo'<input type="submit" name="Send"/>';
echo'</form>';
echo'</body></html>';

?>