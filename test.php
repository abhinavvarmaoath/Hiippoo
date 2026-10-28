<?php
session_start();
$username = $_SESSION['username'];
if (!isset($_SESSION['username'])) {
header('Location: home1.php');
exit;
}
?>
<?php include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?>
      
      <!--main content start--><!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
