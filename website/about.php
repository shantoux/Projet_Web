<!-- Web page containing infos about us and the project -->
<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body class="center">
    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a class="active" href="./search_1.php">New search</a>
        <?php
          if ($_SESSION['status'] == 'annotator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
          }
          if ($_SESSION['status'] == 'validator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
          }
          if ($_SESSION['status'] == 'administrator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>


    <h2  id="pagetitle">The Web Programming Project</h2>

    This project conducted by passionnate <strong>AMI2B students</strong> from Université Paris-Saclay aims at providing every bioinformatics specialist with a <strong>brand new tool</strong> to investigate the meaning and structure of his sequences of interest.
    <br> Based on the best modern existing databases, this tool is reviewed and improved by its own members, thus making it the ideal database to stick to the global scientific consensus in each field.

    <h2>Learn more about us!</h2>

    <!-- Personnal informations about website developers /!\ attention il faut s'assumer !!-->
    <br>
    <div id="shanti">
      <img src="./images/shanti.jpg" alt="Sandra Pijeaud" style="width:360px;height:490px;">
      <br>Shanti
    </div>

    <div id="soun">
      <img src="./images/soundous.jpg" alt="Soundous Bella Baci" style="width:400px;height:400px;">
      <br>Soundous
    </div>

    <div id="ben">
      <img src="./images/benjamin.jpeg" alt="Benjamin Vacus" style="width:228px;height:408px;">
      <br>Benjamin
    </div>

  </body>
</html>
