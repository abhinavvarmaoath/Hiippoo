<?php
require_once __DIR__ . '/db.php';
$conn = db_connect();
      function NewUser() {
       global $conn;
       $username = $_POST['username'];
       $password = $_POST['password'];
       $password = md5($password);
       $email = $_POST['email'];
       $education = $_POST['education'];
       $university = $_POST['university'];
       $stmt = $conn->prepare("INSERT INTO enc (username,password,email,education,university) VALUES (?,?,?,?,?)");
       $stmt->bind_param('sssss', $username, $password, $email, $education, $university);
       $data = $stmt->execute() or die($conn->error);
       if($data) {
header("Location: http://www.hiippoo.com/login.html");
 }

}
function SignUp()
       {
if(!empty($_POST['username']))
 {
  global $conn;
  $stmt = $conn->prepare("SELECT * FROM enc WHERE username = ?");
  $stmt->bind_param('s', $_POST['username']);
  $stmt->execute();
  $query = $stmt->get_result();
header("Location: http://www.hiippoo.com/signup.html");

if(!$row = $query->fetch_array() or die($conn->error))
{
NewUser();
 }
else
{
echo"User";
 }
 }
}
if(isset($_POST['submit']))
{
SignUp();
}
?>