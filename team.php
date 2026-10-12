<html>
<body>

<?php
require_once __DIR__ . '/db.php';
session_start();
$username = $_SESSION['username'];
if (!isset($_SESSION['username'])) {
header('Location: team.php');
exit;
}
$conn = db_connect();

$stmt = $conn->prepare("SELECT * from team where username=?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
         echo "<br> Team:" . htmlspecialchars($row["team"]) . "</br>";
echo " <td><a href='list_files.php?'>SELECT</a></td>
                </tr>";
     }
} else {
     echo "0 results";
}

$conn->close();
?>  

</body>
</html>