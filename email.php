<?php
session_start();
require_once __DIR__ . '/csrf.php';
if (!isset($_SESSION['username'])) {
header('Location: join.php');
exit;
}
?>
<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>
      
      <!--main content start-->
<style>h4 {
    left:1%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;

}

</style>
<h4>
 
 <div align="center">       
<form method="POST"  name='email' action="emailsent.php">
<?php echo csrf_field(); ?>
<ul>
<ul><label for="subject">Complaint Subject</label></ul>
<ul><input type="text" name="subject" size="100" /></ul>
<ul><label for="message">Message</label></ul>
<ul><input type="text" name="message" size="100" /></ul>
<ul><label for="email">Your Service Provider or Institution:</label></ul>
<ul><select name="email">
<option value="prof@example.edu">Prof. Kaikai liu</option>
<option value="support@example.com">AT&T</option>
<option value="support@example.com">Apple support</option>
<option value="support@example.com">DELL</option>
<option value="support@example.com">SJSU Student Union</option>
<option value="support@example.com">Panda Express</option>
<option value="support@example.com">Health Centre</option>
<option value="support@example.com">Fitness Centre</option>
</select></ul>



<ul><input type="submit" name="submit" value="Submit" /></ul>
</ul>
</form>

</div>
</h4>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
