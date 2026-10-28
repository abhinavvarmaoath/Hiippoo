<?php
require_once __DIR__ . '/db.php';
session_start();
$username = $_SESSION['username'];
if (!isset($_SESSION['username'])) {
header('Location: home1.php');
exit;
}
$conn = db_connect();

$stmt = $conn->prepare("SELECT email, education, university FROM enc where username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();


?>
<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?><style>h1 {
    left:20%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;

}
h2 {
    left:20%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 60%;
    width: 100%;

}
</style>
      
      <!--main content start-->
<h1> Welcome to HIIPPOO,     <?php echo htmlspecialchars($username);?></h1>
<p>
<h2>
<?php if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "email: " . htmlspecialchars($row["email"]). " - Education: " . htmlspecialchars($row["education"]). " " . htmlspecialchars($row["university"]). "<br>";
    }
} else {
    echo "0 results";
}
?>
</h2>
</p>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
