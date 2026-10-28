<?php
require_once __DIR__ . '/csrf.php';
session_start();
if (!isset($_SESSION['username'])) {
header('Location: create.php');
exit;
}
?>
<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>
      
      <!--main content start-->
<style>
h3 {
    left:20%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;
}
</style>
<h3>
<form method="post" action="created.php">
<?php echo csrf_field(); ?>
Team Name:<input type="text" name="team"/><br/><br/>
<input type="submit" name="submit" value="Create"/>

</h3>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
