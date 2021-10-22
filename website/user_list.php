<!-- Web page to display, validate, delete users -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users list </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body class="center">
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
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

    <h2 id="pagetitle">
      Users' list
    </h2>

    <form></form>

    <div id = "element1">
      <table class = "center">
        <thead>
          <tr>
            <th>User email</th>
            <th>Role</th>
            <th>Last connexion</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>bob@gmail.com </td>
            <td>annotator </td>
            <td>22/10/2021 18:46</td>
            <td>
              <input type="checkbox" id="Validate" name="validate">
              <label for="validate">Validate</label>
              <br>
              <input type="checkbox" id="Delete" name="delete">
              <label for="delete">Delete</label>
            </td>
            <td>
              <input type="submit" name="save" value="save">
            </td>
          </tr>

        <tbody>
      </table>
    </div>
    </form>
  </body>
</html>
