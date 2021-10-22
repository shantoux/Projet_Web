<!-- Web page containing infos about us and the project -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body>
    <?php
      # TODO: un-hardcode the user role, check in database for the actual role
      $role = "administrator";
      $roles = array("annotator", "validator", "administrator");
    ?>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search_1.php">New search</a>
        <?php
          if (in_array($role, array_slice($roles, 0), true)) {
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
          }
          if (in_array($role, array_slice($roles, 1), true)) {
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
          }
          if (in_array($role, array_slice($roles, 2), true)) {
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
          }
        ?>
        <a class="active" href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

<<<<<<< HEAD
    Benjamin Vacus<br>
    Sandra Pijeaud <br>
    Soundous Bella Baci <br>
=======
    <br>
    <div id="shanti">
      Shanti
    </div>

    <div id="soun">
      Soundous
    </div>

    <div id="ben">
      Benjamin
    </div>
  </dl>
>>>>>>> b4304b7835c96c0b8e7cd204ddbec08171787d80
  </body>
</html>
