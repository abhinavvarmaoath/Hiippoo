<?php
session_start();
require_once __DIR__ . '/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
<style>

body{
background-image: url("bg.jpg");

}
</style>


</head>

<body>



    <header id="top" class="header">
        <div class="text-vertical-center">

<body><b>
<h1 align="center">Log In</h1>
<form method="post" align="center" action="login.php">
<?php echo csrf_field(); ?>
Username:<input type="text" name="username"/><br/><br/>
Password:<input type="password" name="password"/><br/><br/></b>
<input type="submit" name="submit" value="Log In"/>



</body>

</html>
