<!-- Page web de recherche de séquences, accès automatique après authentification authentification -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navigation </title>
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
        <a class="active" href="./search_1.php">New search</a>
        <?php
          if (in_array($role, array_slice($roles, 0), true)) {
            echo "<a href=\"#annotation_main\">Annotate sequence</a>";
          }
          if (in_array($role, array_slice($roles, 1), true)) {
            echo "<a href=\"#validation_main\">Validate annotation</a>";
          }
          if (in_array($role, array_slice($roles, 2), true)) {
            echo "<a href=\"#sequence_atribution_main\">Attribute annotation</a>";
          }
        ?>
        <a href="#about">About</a>
    </div>

    <div class="alert_good">
      <span class="closebtn"
      onclick="this.parentElement.style.display='none';">&times;</span>
      Authentification réussie :)
    </div>
  </body>
</html>
