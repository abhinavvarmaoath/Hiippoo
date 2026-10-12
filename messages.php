<html>
<head>
<meta http-equiv="refresh" content="3">
<title>Messages</title>
</head>
<body>
<?php
require_once __DIR__ . '/db.php';
session_start();
$username = $_SESSION['username'];
$team = $_SESSION['team'];
$link = db_connect();
	$stmt = $link->prepare("select * from messages where team = ?");
	$stmt->bind_param('s', $team);
	$stmt->execute();
	if($result = $stmt->get_result()){
		while($row = mysqli_fetch_row($result))
		{
			echo $row['3'].':'.$row['1'].'</br>';
		}
		mysqli_free_result($result);
	}

mysqli_close($link);	    
?>
</body>
</html>