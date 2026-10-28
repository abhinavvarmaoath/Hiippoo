<?php
session_start();
require_once __DIR__ . '/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

<style>
form {

left:80%;
width:250px;
}
body{
background-image: url("bg.jpg");

}
</style>
</head>

<body>

<h1>SignUp</h1>
<form method="POST"  name='registration'  action="signup.php">
<?php echo csrf_field(); ?>
<ul><b>
<ul><label for="username">User id:</label></ul>
<ul><input type="text" name="username" required size="50" /></ul>
<ul><label for="password">Password:</label></ul>
<ul><input type="password" name="password" required size="50" /></ul>
<ul><label for="email">Email:</label></ul>
<ul><input type="text" name="email" required size="50" /></ul>
<ul><label>Education:</label></ul>
<ul><input type="text" name="education" required size="50" /></ul>
<ul><label>University:</label></ul>
<ul><input type="text" name="university" required size="50" /></ul></b>
<ul><input type="submit" name="submit" value="Submit" /></ul>
</ul>
</form>


        </div>
    </header>



</body>

</html>
