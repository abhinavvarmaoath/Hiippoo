<?php
session_start();
require_once __DIR__ . '/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <h1>Get in touch</h1>

</head>

<body>

    <header id="top" class="header">
        <div class="text-vertical-center">
<form method="POST"  name='email' action="emailsent.php">
<?php echo csrf_field(); ?>
<ul>
<ul><label for="subject">Complaint Subject</label></ul>
<ul><input type="text" name="subject" size="50" /></ul>
<ul><label for="message">Message</label></ul>
<ul><input type="text" name="message" size="50" /></ul>




<ul><label for="email">Your Service Provider or Institution:</label></ul>
<ul><select name="email">
<option value="support@example.com">AT&T</option>
<option value="support@example.com">SJSU</option>
</select></ul>



<ul><input type="submit" name="submit" value="Submit" /></ul>
</ul>
</form>


        </div>
    </header>


</body>

</html>
