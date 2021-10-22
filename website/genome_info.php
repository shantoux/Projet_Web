<!-- Web page to get information about genome -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Genome information </title>
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
      Genome information
    </div>

    <div class="center">
      <table class="table_type2">
        <thead>
          <tr>
            <th colspan=3 class="type2">Genome's name : Ecoli</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>
              box
              <input type="checkbox" id="Select" name="select">
            </td>
          <td>
            ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCGGGCTGA
          </td>
          <td>
            Annotated<br>
            By [annotator]<br>
            on 17-11-2021
          </td>
        </tr>

        <tr>
          <td>ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCGGGCTGA </td>
          <td>
            Annotated<br>
            By [annotator]<br>
            on 17-11-2021
          </td>
        </tr>

        </tbody>
      </table>
    </div>

    Extract: <input type="text" name="extract" value=""></input>
    <a href="path_to_file" download="name_file">
         <button type="button">Download</button>
         </a>

  </body>
</html>
