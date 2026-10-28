<?php
require_once __DIR__ . '/db.php';
session_start();
if (!isset($_SESSION['username'])) {
header('Location: displayteam.php');
exit;
}
// Make sure an ID was passed
if(isset($_GET['id'])) {
// Get the ID
    $id = intval($_GET['id']);
 
    // Make sure the ID is in fact a valid ID
    if($id <= 0) {
        die('The ID is invalid!');
    }
    else {
        // Connect to the database
        $dbLink = db_connect();

        // Fetch the file information
        $query = "
            SELECT `mime`, `name`, `size`, `data`, `team`
            FROM `file`
            WHERE `id` = {$id}";
        $result = $dbLink->query($query);

        if($result) {
            // Make sure the result is valid
            if($result->num_rows == 1) {
            // Get the row
                $row = mysqli_fetch_assoc($result);

                $memberStmt = $dbLink->prepare("SELECT 1 FROM team WHERE username = ? AND team = ?");
                $memberStmt->bind_param('ss', $_SESSION['username'], $row['team']);
                $memberStmt->execute();
                $member = $memberStmt->get_result();
                if (!$member || $member->num_rows == 0) {
                    die('Error! You are not a member of that file\'s team.');
                }

                // Print headers
                header("Content-Type: ". $row['mime']);
                header("Content-Length: ". $row['size']);
                header("Content-Disposition: attachment; filename=". $row['name']);
 
                // Print data
                echo $row['data'];
            }
            else {
                echo 'Error! No image exists with that ID.';
            }
 
            // Free the mysqli resources
            @mysqli_free_result($result);
        }
        else {
            echo "Error! Query failed: <pre>{$dbLink->error}</pre>";
        }
        @mysqli_close($dbLink);
    }
}
else {
    echo 'Error! No ID was passed.';
}
?>