<!-- Web page for the annotation forum -->

<?php session_start();

  // check if user is logged in: else, redirect to login page
  if (!isset($_SESSION['user'])) {
    echo '<script>location.href="login.php"</script>';
  }

?>

<!DOCTYPE html>
<html>

  <!-- Page header -->
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consult annotation</title>
    <link rel="stylesheet" type="text/css" href="./style.css" /s>
  </head>

  <body>
    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'Annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a class=\"active\" href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a class=\"active\" href=\"./consult_annotation.php\">Consult annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a class=\"active\" href=\"./consult_annotation.php\">Consult annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="login.php">Disconnect</a>
        <a class="disc"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
    </div>

    <!-- Display page title -->
    <div id="pagetitle">
      Consult all assigned annotations
    </div>

    <div class="center">
      Bientôt. Le 09/12/2021. Note la date.
    </div>

    <?php
      // import db functions
      include_once 'libphp/dbutils.php';
      connect_db();
    ?>
    
  </body>
</html>
