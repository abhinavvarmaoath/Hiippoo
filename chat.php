<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Chat Room</title>
</head>
<?php
session_start();
$username = $_SESSION['username'];
if (!isset($_SESSION['username'])) {
header('Location: chat.php');
exit;
}

?>
<form action="chatroom.php" method="post">
<br>Please enter your name below:</br>
<input type="text" name="username"/>
<input type="submit" name="Enter"/>
</form>
<body>
</body>
</html>