<!-- Web page for the annotation forum -->

<?php session_start();

  // check if user is logged in: else, redirect to login page
  if (!isset($_SESSION['user'])) {
    echo '<script>location.href="login.php"</script>';
  }

  // import db functions
  include_once 'libphp/dbutils.php';
  connect_db();

  $update = false;

  // remove assignation on validator call
  if (isset($_POST["remove"])) {

    // update annotation status
    $query_annot = "SELECT genome_id, sequence_id, annotator, attempt
    FROM database_projet.annotations
    WHERE status = 'assigned' AND sequence_id = '" . $_GET["sid"] . "';";
    $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());

    if (pg_num_rows($result_annot) > 0) {

      $values_status = array();
      $values_status['status'] = 'rejected';

      $condition = array();
      $condition['genome_id'] = pg_fetch_result($result_annot, 0, 0);
      $condition['sequence_id'] = pg_fetch_result($result_annot, 0, 1);
      $condition['annotator'] = pg_fetch_result($result_annot, 0, 2);
      $condition['attempt'] = pg_fetch_result($result_annot, 0, 3);

      $update = pg_update($db_conn, 'database_projet.annotations', $values_status, $condition) or die('Query failed with exception: ' . pg_last_error());
    }
  }

?>

<!DOCTYPE html>
<html>

  <!-- Page header -->
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consult annotation</title>
    <link rel="stylesheet" type="text/css" href="./style.css" /s>
  </head>

  <!-- display menu options depending of the user's role -->
  <body class="center">
    <div class="topnav">
        <a href="./search.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'Annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a class=\"active\" href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a class=\"active\" href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a class=\"active\" href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="login.php">Disconnect</a>
        <a class="role"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
    </div>

    <!-- Display fancy box -->
    <div class="fancy_box" style="width:60%;">

      <!-- Display page title -->
      <div id="pagetitle">
        Consult all assigned annotations
      </div>

      <div class="center">
        <br><br>
      </div>

      <?php
        // display good alert if an annotation has been successfully removed
        if ($update) {
          echo "<br> <div class=\"alert_good\">
            <span class=\"closebtn\"
            onclick=\"this.parentElement.style.display='none';\">&times;</span>
            Successfully removed assignation.
          </div>";
        }

        // retrieve date of now
        $query_time = "SELECT now();";
        $result_time = pg_query($db_conn, $query_time) or die('Query failed with exception: ' . pg_last_error());
        $current_date = pg_fetch_result($result_time, 0, 0);

        // retrieve all pending annotations
        $query_annots = "SELECT genome_id, sequence_id, annotator, attempt, assignation_date
        FROM database_projet.annotations
        WHERE status = 'assigned' ORDER BY assignation_date ASC;";
        $result_annots = pg_query($db_conn, $query_annots) or die('Query failed with exception: ' . pg_last_error());

        //display table with all pending annotations
        echo '<div id="element1">';

        if (pg_num_rows($result_annots) > 0) {

          // display first line
          echo '<table class="table_type1">';
          echo '<thead>';
          echo '<tr>';
          echo '<th>Annotator</th><th>Genome</th><th>Sequence</th><th>Attempt number</th><th>Assignation date<br>of last attempt</th><th>Remove assignation</th>';
          echo '</tr>';
          echo '</thead>';
          echo ' <tbody>';

          // loop on all currently assigned annotation
          while ($annotation = pg_fetch_array($result_annots)) {
            echo "<tr>";

            // display annotator
            echo "<td>" . $annotation["annotator"] . "</td>";

            // display genome name
            echo '<td>' . $annotation["genome_id"] . '</td>';

            // display sequence identifier
            echo '<td>' . $annotation["sequence_id"] . '</td>';

            // display attempts number with color accord
            echo '<td>';
            if ($annotation["attempt"] > 2 && $annotation["attempt"] < 5) {
              echo '<span style="color:orange;">';
            }
            if ($annotation["attempt"] > 4) {
              echo '<span style="color:red;">';
            }
            echo $annotation["attempt"];
            if ($annotation["attempt"] > 2) {
              echo '</span>';
            }
            echo '</td>';

            // display assignation date
            echo '<td>';
            // compute time difference
            $date_1 = new DateTime(substr($annotation["assignation_date"], 0, 19));
            $date_2 = new DateTime(substr($current_date, 0, 19));
            $interval = $date_1->diff($date_2);
            $diff = $interval->format('%d');
            // change color to red if assigned more than 2 weeks ago
            if ($diff > 14) {
              echo '<span style="color:red;">';
            }
            echo substr($annotation["assignation_date"], 0, 19);
            if ($diff > 14) {
              echo '</span>';
            }
            echo '</td>';

            // display remove button
            echo '<form action="consult_annotation.php?sid=' . $annotation["sequence_id"] . '" method="post">';
            echo '<td><input class="button_neutral" type="submit" name="remove" value="&#10008"></td>';
            echo '</form>';
            echo "</tr>";
          }

          echo '</tbody>';
          echo '</table>';

        }

        else {
          echo "<div class=\"alert_neutral\">
          There is no pending annotation.</div>";
        }


        ?>

      </div>
    </div>
  </body>
</html>
