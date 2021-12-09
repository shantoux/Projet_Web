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
  <a class="disc"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
</div>

<h3 id="pagetitle"> Sequences annotation </h3>
Welcome to the annotations factory. Here you will find a list of sequences of which you have been assigned the annotation.
<br> Let's take a moment to <strong>Thank You!</strong> for your work, contributing to the annotation of the database is the best way to help us improve the quality of the search.
<br>
<br>
<h3 id="pageundertitle" class="center"> Sequences waiting to be annotated </h3>
<br>

<!-- Table to display sequences assignated for annotation -->

<div id="element1">
  <table class="table_type1">
  <colgroup>
        <col style="width: 10%">
        <col style="width: 10%">
        <col style="width: 10%">
        <col style="width: 10%">
      </colgroup>
    <thead>
      <tr>
        <th>Genomes</th>
        <th>Sequences</th>
        <th>Action</th>
        <th>Attempt</th>
      </tr>
    </thead>

    <tbody>
      <?php
      include_once 'libphp/dbutils.php';
      connect_db();
      $query = "SELECT a.genome_id, a.sequence_id, a.attempt
        FROM database_projet.annotations a
        WHERE a.annotator ='" . $_SESSION['user'] . "' and a.status='assigned';";
      $result = pg_query($db_conn, $query);
      if ($result != false) {
        while ($rows = pg_fetch_array($result)) {
          echo "<tr>";
          echo "<td>" . $rows["genome_id"] . "</td>";
          echo '<td>' . $rows["sequence_id"] . '</td>';
          # Review annotation
          echo '<td> <input type="button" class="button_active" value="Annotate" onclick="location.href=\'sequence_annotation.php?gid=' . $rows['genome_id'] . '&sid=' . $rows["sequence_id"] .'&att='.$rows['attempt'].'\';"/></td>';
          echo '<td>' . $rows["attempt"] . '</td>';
          echo "</tr>";
        }
      } else {
        echo "
        <tr>
        <td colspan='4'>Something went wrong with the query</td>
        </tr>
    ";
      }
      ?>
    </tbody>
  </table>

</div>

<br>
<h3 id="pageundertitle" class="center"> Sequences already annotated </h3>
<br>

<div id="element1">
  <table class="table_type1">
  <colgroup>
        <col style="width: 10%">
        <col style="width: 10%">
        <col style="width: 10%">
        <col style="width: 10%">
        <col style="width: 10%">
      </colgroup>
    <thead>
      <tr>
        <th>Genomes</th>
        <th>Sequences</th>
        <th>Validator's comment</th>
        <th>Annotation status</th>
        <th>Attempt</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $query = "SELECT a.genome_id, a.sequence_id, a.comments, a.status, a.attempt
      FROM database_projet.annotations a
      WHERE a.annotator ='" . $_SESSION['user'] . "' and a.status!='assigned'
      ORDER BY status;";
      $result = pg_query($db_conn, $query);

      if ($result != false) {
        for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++){
          $genome_id = pg_fetch_result($result, $res_nb,0);
          $seq_id = pg_fetch_result($result, $res_nb,1);
          $comment = pg_fetch_result($result, $res_nb,2);
          $status = pg_fetch_result($result, $res_nb,3);
          $attempt = pg_fetch_result($result, $res_nb,4);
          echo '<tr><td>';
          echo $genome_id;
          echo '</td><td>';
          echo $seq_id;
          echo '</td><td>';
          # Review annotation
          echo $comment;
          echo '</td>';
          if($status == 'rejected'){
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
          echo '</td></tr>';
        }
      } else {
        echo "
        <tr>
        <td colspan='4'>Something went wrong with the query</td>
        </tr>
    ";
      }
      ?>
    </tbody>
  </table>

</div>

</body>

</html>
