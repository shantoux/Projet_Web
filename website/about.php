<!-- Web page containing infos about us and the project -->
<?php session_start();?>

<!DOCTYPE html>
<html>

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
          if ($_SESSION['role'] == 'annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }

        ?>
        <a class="active" href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
    </div>


    <h2  id="pagetitle">The Web Programming Project</h2>

    This project conducted by passionnate <strong>AMI2B students</strong> from Université Paris-Saclay aims at providing every bioinformatics specialist with a <strong>brand new tool</strong> to investigate the meaning and structure of his sequences of interest.
    <br> Based on the best modern existing databases, this tool is reviewed and improved by its own members, thus making it the ideal database to stick to the global scientific consensus in each field.

    <h2>Learn more about us!</h2>

    <!-- Personnal informations about website developers /!\ attention il faut s'assumer !!-->
    <br>
    <div>
      <img src="./images/group_image.jpg" alt="Group picture" style="width:1242px;height:631px;">
      <br>From left to right : Soundous Bella Baci, Benjamin Vacus, Sandra Pijeaud
    </div>

  </body>
</html>
