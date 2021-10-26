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
    <br> <br>

    <!-- Table to display sequences assignated for annotation -->

    <?php
      $assignation_date = "29-04-2020";
      $sequence = "ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCGGGCTGA";
    ?>
    
    <div class = "table_type1">
      <table>
        <colgroup>
          <col style="width: 20%">
          <col style="width: 75%">
          <col style="width: auto">
        </colgroup>
        <thead>
            <tr>
                <th>Assignation date</th>
                <th>Sequences</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td> <?php echo $assignation_date; ?> </td>
                <td>
                  <?php
                    echo substr($sequence, 0, 50);
                    if (strlen($sequence) > 50) {
                      echo "...";
                    }
                  ?>
                </td>
                <td>
                  <form action="./annotation_2.php">
                    <button type="submit">Annotate</button>
                  </form>
                </td>
            </tr>

            <tbody>
      </table>
    </div>

  </body>
</html>
