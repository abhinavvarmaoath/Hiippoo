<?php
require_once __DIR__ . '/db.php';
session_start();
$username = $_SESSION['username'];

if (!isset($_SESSION['username'])) {
header('Location: displayteam.php');
exit;
}
$conn = db_connect();

$stmt = $conn->prepare("SELECT * from team where username=?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();



?>  


<html>
<body>
<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>

<style>h4 {
    left:9%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 20%;
    width: 100%;
}
body {
    background-image: url("");
   
}
</style>
      
      <!--main content start--><h4 align="center"><?php
if ($result->num_rows > 0) {
     // output data of each row
     while($row = $result->fetch_assoc()) {
         echo "<br> Team:" . htmlspecialchars($row["team"]) . "</br>";
         $teamid = $row["team"];

echo " <td><a href='chatroom.php?teamid=" . urlencode($teamid) . "'>SELECT</a></td>
                </tr>";
     }
} else {
     echo "0 results";
}

$conn->close();
?>	</h4><!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
