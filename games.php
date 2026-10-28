<?php
session_start();
$username = $_SESSION['username'];
if (!isset($_SESSION['username'])) {
header('Location: games.php');
exit;
}
?>
<?php $afterBodyOpen = '<script src="//static.miniclipcdn.com/js/game-embed.js"></script>'; include __DIR__ . '/partials/head.php'; ?>
      <!--header end-->

      <?php include __DIR__ . '/partials/sidebar.php'; ?><style>h1 {
    left:20%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 30%;
    width: 100%;

}
h2 {
    left:40%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 40%;
    width: 100%;

}
h3 {
    left:30%;
    line-height: 60px;
    color:black;
    margin-top: -100px;
    position: absolute;
    top: 150%;
    width: 100%;

}
</style>
      
      <!--main content start-->
<h1> Welcome to HIIPPOO Gaming     <?php echo htmlspecialchars($username);?></h1><p></p><p></p><h2>
<div class="miniclip-game-embed" data-game-name="supercar-showdown" data-theme="0" data-width="800" data-height="600" data-language="en"><a href="http://www.miniclip.com/games/supercar-showdown/"></a></div><p>
<p style="text-align:center;"><a href="http://www.miniclip.com/games/supercar-showdown/" target="_blank"></a> / More <a href="http://www.miniclip.com/games/genre-477/" target="_blank">Racing games</a></p></h2></p>
<h3><div class="miniclip-game-embed" data-game-name="cut-the-rope-time-travel" data-theme="0" data-width="1024" data-height="576" data-language="en"><a href="http://www.miniclip.com/games/cut-the-rope-time-travel/">Play Cut the Rope Time Travel</a></div>
<p style="text-align:center;"><a href="http://www.miniclip.com/games/cut-the-rope-time-travel/" target="_blank">Play Cut the Rope Time Travel</a> / More <a href="http://www.miniclip.com/games/genre-1/" target="_blank">Puzzle games</a></p>
</h3>
<!--main content end-->
  </section>

  <!-- container section start -->

    <?php include __DIR__ . '/partials/footer.php'; ?>
