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
       $query = "INSERT INTO enc (username,password,email,education,university) VALUES ('$username','$password','$email','$education','$university')";
       $data = $conn->query($query) or die($conn->error);
       if($data) {
header("Location: http://www.hiippoo.com/login.html");
 }

}
function SignUp()
       {
if(!empty($_POST['username']))
 {
  global $conn;
  $query = $conn->query("SELECT * FROM enc WHERE username = '$_POST[username]'");
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