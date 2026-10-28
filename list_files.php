<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
// Connect to the database
session_start();
if (!isset($_SESSION['username'])) {
header('Location: displayteam.php');
exit;
}
$username = $_SESSION['username'];
$team = $_GET["teamid"];
$dbLink = db_connect();
$memberStmt = $dbLink->prepare("SELECT 1 FROM team WHERE username = ? AND team = ?");
$memberStmt->bind_param('ss', $username, $team);
$memberStmt->execute();
$member = $memberStmt->get_result();
if (!$member || $member->num_rows == 0) {
    die('Error! You are not a member of that team.');
}
$_SESSION['team'] = $team;

// Query for a list of all existing files
$stmt = $dbLink->prepare("SELECT username,id, name, mime, size, created FROM file where team = ?");
$stmt->bind_param('s', $team);
$stmt->execute();
$result = $stmt->get_result();
 ?>
<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>
      
      <!--main content start--><!--main content end-->
  </section>

  <!-- container section start -->
<style>h4 {
    left:10%;
    line-height: 100px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;

}
h3{
    left:15%;
    line-height: 100px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 100%;
    width: 10%;

}
</style>
<h4><div align="center"><?php
if($result) {
    // Make sure there are some files in there
    if($result->num_rows == 0) {
        echo '<p><p>There are no files in the database</p></p>';
    }
    else {
        // Print the top of a table
        echo '<p><p><table width="100%">
                <tr><td><b>Username</b></td>
                    <td><b>Name</b></td>
                    <td><b>Mime</b></td>
                    <td><b>Size (bytes)</b></td>
                    <td><b>Created</b></td>
                    <td><b>&nbsp;</b></td>
                </tr></p></p>';
 
        // Print each file
        while($row = $result->fetch_assoc()) {
            echo "
                <tr><td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['mime']) . "</td>
                    <td>{$row['size']}</td>
                    <td>{$row['created']}</td>
                    <td><a href='get_file.php?id={$row['id']}'>Download</a></td>
                </tr>";

        }
 
        // Close table
        echo '</table>';
    }
 
    // Free the result
    $result->free();
}
else
{
    echo 'Error! SQL query failed:';
    echo "<pre>{$dbLink->error}</pre>";
}
 
// Close the mysql connection
$dbLink->close();
?></div>
</h4>
<h3><b> Add Files in the Group to share!</b>
<form action="add_file.php" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="file" name="uploaded_file"><br>
        <input type="submit" value="Upload file">
    </form>
</h3>
    <?php include __DIR__ . '/partials/footer.php'; ?>
