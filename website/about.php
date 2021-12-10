<!-- Web page containing infos about us and the project -->
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
    <title>About </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">
    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'Annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a class="active" href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
        <a class="role"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
    </div>

    <!-- Display page title -->
    <h2  id="pagetitle">The Web Programming Project</h2>

    This project conducted by passionnate <strong>AMI2B students</strong> from Université Paris-Saclay aims at providing every bioinformatics specialist with a <strong>brand new tool</strong> to investigate the meaning and structure of his sequences of interest.
    <br> Based on the best modern existing databases, this tool is reviewed and improved by its own members, thus making it the ideal database to stick to the global scientific consensus in each field.

    <h2>Learn more about us!</h2>

    <!-- Personnal informations about website developers -->
    <br>
    <div>
      <i>From left to right : Soundous Bella Baci, Benjamin Vacus, Sandra Pijeaud</i><br><br>
      <img src="./images/group_image.jpg" alt="Group picture" style="width:800px;height:600px;">
    </div>

  </body>
</html>
