<!-- Web page to annotate sequences -->
<?php session_start();

// check if user is logged in: else, redirect to login page
if (!isset($_SESSION['user'])) {
  echo '<script>location.href="login.php"</script>';
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sequences annotation </title>
  <link rel="stylesheet" type="text/css" href="./style.css" />
</head>

<body class="center">

  <!-- display menu options depending of the user's role -->
  <div class="topnav">
    <a href="./search.php">New search</a>
    <?php
    if ($_SESSION['role'] == 'Annotator') {
      echo "<a class=\"active\" href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
    }
    if ($_SESSION['role'] == 'Validator') {
      echo "<a class=\"active\" href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
      echo "<a href=\"./consult_annotation.php\">Consult</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
    }
    if ($_SESSION['role'] == 'Administrator') {
      echo "<a class=\"active\" href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
      echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
      echo "<a href=\"./consult_annotation.php\">Consult</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
      echo "<a href=\"./user_list.php\">Users' List</a>";
    }
    ?>
    <a href="about.php">About</a>
    <a class="disc" href="disconnect.php">Disconnect</a>
    <a class="role"><?php echo $_SESSION['first_name'] ?> - <?php echo $_SESSION['role'] ?> </a>
  </div>

  <!-- Display fancy box -->
  <div class="fancy_box" style="width:80%;">

    <!-- Display page title -->
    <div id="pagetitle"> Sequences annotation </div>
    <br><br>
    <!-- Display introductory message -->
    <div id="element1">Welcome to the annotations factory. Here you will find a list of sequences of which you have been assigned the annotation.
      <br> Let's take a moment to <strong>Thank You!</strong> for your work, contributing to the annotation of the database is the best way to help us improve the quality of the search.
    </div>

    <br>
    <br>

    <!--////////////////////////////////////////////////////////////////////////
    //              Annotation assigned to the logged-in annotator
    ////////////////////////////////////////////////////////////////////////-->


    <!--Display under title -->
    <h3 id="pageundertitle" class="center"> Sequences waiting to be annotated </h3>
    <br>

    <!-- Table to display sequences assignated for annotation -->

    <div id="element1">
      <table class="table_type1">
        <?php
        // import db functions
        include_once 'libphp/dbutils.php';
        connect_db();

        //Query to retrieve information about the assignation of an annotation
        // to a specific annotator
        $query = "SELECT a.genome_id, a.sequence_id, a.attempt, a.annotator, a.assignation_date
            FROM database_projet.annotations a
            WHERE a.annotator ='" . $_SESSION['user'] . "' and a.status='assigned'
            ORDER BY assignation_date;";
        $result = pg_query($db_conn, $query);

        if (pg_num_rows($result) > 0) {

          // If the annotator have assigned sequences, display table
          echo "<thead>";
          echo "<tr>";
          echo "<th>Assigned on</th>";
          echo "<th>Genomes</th>";
          echo "<th>Sequences</th>";
          echo "<th>Action</th>";
          echo "<th>Attempt</th>";
          echo "</tr>";
          echo "</thead>";
          echo "<tbody>";

          if ($result != false) {
            while ($rows = pg_fetch_array($result)) {
              echo "<tr>";
              echo "<td>" . date('d-m-o H:i', strtotime($rows["assignation_date"])) . "</td>";
              echo "<td>" . $rows["genome_id"] . "</td>";
              echo '<td>' . $rows["sequence_id"] . '</td>';
              # Review annotation
              echo '<td> <input type="button" class="button_ok" value="Annotate" onclick="location.href=\'sequence_annotation.php?gid=' . $rows['genome_id'] . '&sid=' . $rows["sequence_id"] . '&att=' . $rows['attempt'] . '&annotator=' . $rows['annotator'] . '\';"/></td>';
              echo '<td>' . $rows["attempt"] . '</td>';
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='4'>Something went wrong with the query</td></tr>";
          }
        } else {
          echo "<div class=\"alert_neutral\">
              You were not assigned a sequence yet!</div>";
        }
        ?>

        </tbody>
      </table>

    </div>

    <!--////////////////////////////////////////////////////////////////////////
    //                    Display of previous annotations
    ////////////////////////////////////////////////////////////////////////-->

    <br>

    <!--Display under title -->
    <h3 id="pageundertitle" class="center"> Sequences already annotated </h3>

    <br>

    <!-- Table to display previously assigned annotations -->

    <div id="element1">
      <table class="table_type1">
        <?php
        //Query to retrieve informations about annotations that are not currently
        // assigned to the the logged-in annotator anymore
        $query = "SELECT a.genome_id, a.sequence_id, a.comments, a.status, a.attempt, a.assignation_date, a.annotator
            FROM database_projet.annotations a
            WHERE a.annotator ='" . $_SESSION['user'] . "' and a.status!='assigned'
            ORDER BY assignation_date DESC;";
        $result = pg_query($db_conn, $query);

        if (pg_num_rows($result) > 0) {
          // If the annotator have had validated or rejected annotations, display table
          echo "<thead>";
          echo "<tr>";
          echo "<th>Genomes</th>";
          echo "<th>Sequences</th>";
          echo "<th>Validator's comment</th>";
          echo "<th>Annotation status</th>";
          echo "<th>Attempt</th>";
          echo "<th>Assignation date</th>";
          echo "</tr>";
          echo "</thead>";
          echo "<tbody>";

          if ($result != false) {

            for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++) {

              $genome_id = pg_fetch_result($result, $res_nb, 0);
              $seq_id = pg_fetch_result($result, $res_nb, 1);
              $comment = pg_fetch_result($result, $res_nb, 2);
              $status = pg_fetch_result($result, $res_nb, 3);
              $attempt = pg_fetch_result($result, $res_nb, 4);
              $assignation_date = pg_fetch_result($result, $res_nb, 5);
              $annotator = pg_fetch_result($result, $res_nb, 6);
              echo '<tr><td>';
              echo $genome_id;
              echo '</td><td>';
              echo '<a href="./sequence_annotation.php?gid=' . $genome_id . '&sid=' . $seq_id . '&att=' . $attempt . '&annotator=' . $annotator . '"style="color: #4F8E8D">' . $seq_id . '</a>';
              echo '</td><td>';
              # Review annotation
              echo $comment;
              echo '</td>';
              if ($status == 'rejected') {
                echo '<td><span style="color:red;">';
                echo $status;
                echo '</span></td>';
              } else {
                echo '<td>';
                echo $status;
                echo '</td>';
              }
              echo '<td>';
              echo $attempt;
              echo '</td><td>';
              echo date('d-m-o H:i', strtotime($assignation_date));
              echo '</td></tr>';
            }
          } else {
            echo "<tr><td colspan='4'>Something went wrong with the query</td></tr>";
          }
        } else {
          echo "<div class=\"alert_neutral\">
              You do not have any previous work yet!</div>";
        }
        echo "</tbody>";

        ?>
      </table>
    </div>
  </div>
</body>
</html>
