<!-- Web page to validate sequence annotations -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annotation validation </title>
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
            echo "<a class=\"active\" href=\"./validation_1.php\">Validate annotation</a>";
          }
          if (in_array($role, array_slice($roles, 1), true)) {
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

    <h2 id="pagetitle"> Annotations waiting for validation </h2>

    <!-- Table to display annotations waiting for validation -->

    <!-- TODO: retrieve anotations from the database. The following is hardcoded data to display pages in the meantime. -->
    <?php
      $annotations = array();
      array_push($annotations, array("29-04-2020", "ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCGGGCTGA", "Bob"));
      array_push($annotations, array("15-04-2020", "AAATTAGCCCTAGCT", "Bobby"));
    ?>

    <div class = "table_type1">
      <table>
        <colgroup>
          <col style="width: 13%">
          <col style="width: 25%">
          <col style="width: 10%">
          <col style="width: 15%">
          <col style="width: 18%">
          <col style="width: auto">
        </colgroup>
        <thead>
          <tr>
            <th>Submission date</th>
            <th>Sequences</th>
            <th>Annotator</th>
            <th>Comments</th>
            <th colspan=2>Action</th>
          </tr>
        </thead>

        <tbody>
          <?php
            # Print a new line for each available annotation.
            foreach ($annotations as $annotation) {
              echo "<tr>";
              # Annotation submission date
              echo "<td>" . $annotation[0] . "</td>";
              # Annotation sequence (cut)
              echo "<td>" . substr($annotation[1], 0, 25);
              if (strlen($annotation[1]) > 25) {
                echo "...";
              }
              echo "</td>";
              # Annotator
              echo "<td>" . $annotation[2] . "</td>";
              # Comment for validation or refusal
              echo "<td> <input type=\"text\" name=\"comments\"> </td>";
              # Review annotation
              echo "<td> <input type=\"submit\" value=\"Review annotation\" name=\"review\"> </td>";
              # Validate / Refuse annotation
              echo "<td>";
              echo "<input type=\"submit\" value=\"Validate\" name=\"validate\">  ";
              echo "<input type=\"submit\" value=\"Refuse\" name=\"refuse\">";
              echo "</td>";
              echo "</tr>";
            }
          ?>
        <tbody>
      </table>
    </div>
  </body>
</html>
