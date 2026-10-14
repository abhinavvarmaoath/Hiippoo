<?php
require_once __DIR__ . '/db.php';
session_start();
$username = $_SESSION['username'];
if (!isset($_SESSION['username'])) {
header('Location: emailsent.php');
exit;
}
$conn = db_connect();
$email    = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$from = getenv('CONTACT_FROM_EMAIL') ?: 'noreply@example.com';
$headers = 'From: ' . $from . "\r\n" .
    'Reply-To: ' . $from . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
$result =mail($email, $subject, $message, $headers);
     

if(!$result) {   
     echo "Error";   
} else {
    echo "Success";
}
?> 
<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?><style>h3 {
    left:20%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;

}
</style>
      
      <!--main content start-->
<h3> <?php
if(!$result) {
     echo "Error";   
} else {
    echo "Your mail request has been sent please let us get back to you. Thank you for being patient.";
}?></h3>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
