<!-- Web page to annotate sequences -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sequences annotation </title>
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
            echo "<a class=\"active\" href=\"./annotation_1.php\">Annotate sequence</a>";
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
    
    <h2>Sequences annotation</h2>
    <br> Welcome to the annotations factory. Here you will find a list of sequences of which you have been assigned the annotation.
    <br> Let's take a moment to <strong>Thank You!</strong> for your work, contributing to the annotation of the database is the best way to help us improve the quality of the search.
          
    <div class = "table_soun">
      <table>
        <thead>
            <tr>
                <th>Assignation date</th>
                <th>Sequences</th>
                <th></th>
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
