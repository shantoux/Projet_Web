<!-- Web page to validate sequence annotations -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annotation validation </title>
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
            echo "<a class=\"active\" href=\"./validation_1.php\">Validate annotation</a>";
          }
          if (in_array($role, array_slice($roles, 2), true)) {
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

    <div id="pagetitle">
      Pending annotations
    </div>
    <div class = "table_soun">
      <table>
        <thead>
            <tr>
                <th>Submission date</th>
                <th>Sequences</th>
                <th>Annotator</th>
                <th>Comments</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>29-04-2020 </td>
                <td>ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCGGGCTGA </td>
                <td>Bob</td>
                <td>
                  <input type="text" name="comments"><br>
                  <input type="submit" name="save">
                </td>
                <td>
                  <input type="checkbox" id="Validate" name="validate">
                  <label for="validate">Validate</label>
                  <br>
                  <input type="checkbox" id="Delete" name="delete">
                  <label for="delete">Delete</label>
                </td>
            </tr>

            <tbody>
      </table>
    </div>
  </body>
</html>
