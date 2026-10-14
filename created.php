<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
session_start();
if (!isset($_SESSION['username'])) {
header('Location: created.php');
exit;
}
csrf_verify();
$username = $_SESSION['username'];
$team=$_POST['team'];
$dbLink = db_connect();
$existsStmt = $dbLink->prepare("SELECT 1 FROM team WHERE team = ? LIMIT 1");
$existsStmt->bind_param('s', $team);
$existsStmt->execute();
$teamExists = $existsStmt->get_result()->num_rows > 0;
if ($teamExists) {
    $result1 = false;
} else {
    $stmt = $dbLink->prepare("INSERT INTO `team` (`username`,`team`) VALUES (?,?)");
    $stmt->bind_param('ss', $username, $team);
            // Execute the query
     $result1 = $stmt->execute();
}
?>

<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>
      
      <!--main content start-->
<style>h1 {
    left:25%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;

}
</style>
<h1>
<?php
        if ($teamExists) {
            echo 'Error! A team named "' . htmlspecialchars($team) . '" already exists. Join it instead, or pick a different name.';
        }
        elseif($result1) {
            echo 'Team Created!';
        }
        else {
            echo 'Error! Failed to insert the file'
               . "<pre>{$dbLink->error}</pre>";
        }
?>


</h1>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
