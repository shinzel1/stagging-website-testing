<?php include 'includes/header.php'; ?>
<main>
  <?php
  // Routing logic
  $page = isset($_GET['page']) ? $_GET['page'] : 'home';
  $file = $page . ".php";

  if (file_exists($file)) {
    include $file;
  } else {
    include "pages/404.php";
  }
  ?>
</main>

<?php include 'includes/footer.php'; ?>
</body>

</html>