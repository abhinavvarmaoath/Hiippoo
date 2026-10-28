<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';
session_start();
if (!isset($_SESSION['username'])) {
header('Location: displayteam.php');
exit;
}
$username = $_SESSION['username'];
$team = $_SESSION["team"];
// Check if a file has been uploaded
if(isset($_FILES['uploaded_file'])) {
    csrf_verify();
    // Make sure the file was sent without errors
    if($_FILES['uploaded_file']['error'] == 0) {
        // Connect to the database
        $dbLink = db_connect();

        // Gather all required data
        $name = $_FILES['uploaded_file']['name'];
        $mime = $_FILES['uploaded_file']['type'];
        $data = file_get_contents($_FILES['uploaded_file']['tmp_name']);
        $size = intval($_FILES['uploaded_file']['size']);
        $username = $_SESSION["username"];
        

        // Create the SQL query
        $stmt = $dbLink->prepare("INSERT INTO `file` (`username`,`team`,`name`, `mime`, `size`, `data`, `created`) VALUES (?,?,?,?,?,?,NOW())");
        $stmt->bind_param('ssssss', $username, $team, $name, $mime, $size, $data);

        // Execute the query
        $result = $stmt->execute();

        // Check if it was successfull
       
?>

<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>
      
      <!--main content start-->
<style>h2 {
    left:15%;
    line-height: 100px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;

}
</style>
<h2>
<?php
 if($result) {
            echo 'Success! Your file was successfully added!';
        }
        else {
            echo 'Error! Failed to insert the file'
               . "<pre>{$dbLink->error}</pre>";
        }

        // Close the mysql connection
        $dbLink->close();
    }
    else {
        echo 'An error accured while the file was being uploaded. '
           . 'Error code: '. intval($_FILES['uploaded_file']['error']);
    }
}
else {
    echo 'Error! A file was not sent!';
}
 
// Echo a link back to the main page
echo '<p>Click <a href="displayteam.php">here</a> Team</p>';
?>
</h2>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
