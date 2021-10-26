<!-- Web page to annotate a sequence -->

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
            echo "<a class=\"active\" href=\"./annotation_1.php\">Annotate sequence</a>";
          }
          if (in_array($role, array_slice($roles, 1), true)) {
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
          }
          if (in_array($role, array_slice($roles, 1), true)) {
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

    <h2 id="pagetitle">
      Sequence Annotation
    </h2>

    <form>
    <div class = "table_type1">
        
      <table>
        <thead>
          <tr>
            <th>Sequence</th>
            <th>Annotation</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCGGGCTGA </td>
            <td>
              Genome type : <input type="text" name="genome_type"><br>
              Gene Biotype : <input type="text" name="gene_biotype"><br>
              Transcript Biotype : <input type="text" name="transcript_biotype"><br>
              Gene symbol : <input type="text" name="gene_symbol"><br>
              Description : <input type="text" name="gene_description"><br>


            </td>
          </tr>
        <tbody>
      </table>
    </div>
    <input type="submit" name="save" value="save">
    </form>
  </body>
</html>
