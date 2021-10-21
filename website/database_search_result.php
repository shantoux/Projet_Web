<!DOCTYPE html>
<html>
<head> <meta charset="UTF-8">
  <title>Database search result</title>
  <link rel="stylesheet" type="text/css" href="pw_style.css" />
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
      <a href="about.php">About</a>
      <a class="disc" href="Login_page1.php">Disconnect</a>
  </div>
  <br>

  <div id="element1">
    Search results
  </div>

  Genome's type : [insert type protein or nucleotides]<br>
  <h2>Results:</h2>

<div class="table-wrapper">
  <table class="ft-table">

    <thead>
        <tr>
            <th>Genome</th>
            <th>Species</th>
            <th>Sequences</th>
        </tr>
        </thead>

  </table>
</div>
</body>
</html>
