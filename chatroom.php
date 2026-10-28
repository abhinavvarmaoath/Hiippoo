<?php
require_once __DIR__ . '/db.php';
session_start();
if (!isset($_SESSION['username'])) {
header('Location: chatroom1.php');
exit;
}
$username = $_SESSION['username'];
$teamid = $_GET['teamid'];
$conn = db_connect();
$memberStmt = $conn->prepare("SELECT 1 FROM team WHERE username = ? AND team = ?");
$memberStmt->bind_param('ss', $username, $teamid);
$memberStmt->execute();
$member = $memberStmt->get_result();
if (!$member || $member->num_rows == 0) {
    die('Error! You are not a member of that team.');
}
$_SESSION["team"] = $teamid;
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<style>
body {
    background-image: url(".jpg");
    background-color: #cccccc;
}
</style>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>
      <style>h7{
left:15%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 32%;
    width: 100%;

}
</style>
      <!--main content start--><h7>
    <iframe src="http://hiippoo.com/messages.php" width="1500" height="600" align="middle">
<p>Your browser does not support iframes.</p>
</iframe>
<p>
  <iframe src="http://hiippoo.com/newmessage.php" width="1500" height="100" align="middle">
  <p>Your browser does not support iframes.</p>
  </iframe>
</p>
</h7>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
