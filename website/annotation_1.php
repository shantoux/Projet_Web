<!-- Web page to annotate sequences -->
<?php session_start(); ?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sequences annotation </title>
  <link rel="stylesheet" type="text/css" href="./pw_style.css" />
</head>
<!-- display menu options depending of the user's role -->
<div class="topnav">
  <a href="./search_1.php">New search</a>
  <?php
  if ($_SESSION['status'] == 'annotator') {
    echo "<a class=\"active\" href=\"./annotation_1.php\">Annotate sequence</a>";
  }
  if ($_SESSION['status'] == 'validator') {
    echo "<a class=\"active\" href=\"./annotation_1.php\">Annotate sequence</a>";
    echo "<a href=\"./validation_1.php\">Validate annotation</a>";
  }
  if ($_SESSION['status'] == 'administrator') {
    echo "<a class=\"active\" href=\"./annotation_1.php\">Annotate sequence</a>";
    echo "<a href=\"./validation_1.php\">Validate annotation</a>";
    echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
  }
  ?>
  <a href="about.php">About</a>
  <a class="disc" href="Login_page1.php">Disconnect</a>
</div>

<h2 id="pagetitle"> Sequences annotation </h2>
Welcome to the annotations factory. Here you will find a list of sequences of which you have been assigned the annotation.
<br> Let's take a moment to <strong>Thank You!</strong> for your work, contributing to the annotation of the database is the best way to help us improve the quality of the search.
<br> 
<br>
<br> Sequences waiting to be annotated

<!-- Table to display sequences assignated for annotation -->

<div id="element1">
  <table class="table_type1">
  <colgroup>
        <col style="width: 5%">
        <col style="width: 5%">
        <col style="width: 10%">
      </colgroup>
    <thead>
      <tr>
        <th>Génomes</th>
        <th>Sequences</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      include_once 'libphp/dbutils.php';
      connect_db();
      $query = "SELECT a.genome_id, a.sequence_id
        FROM annotation_seq.annotations a
        WHERE a.annotator ='" . $_SESSION['user'] . "' and a.status is null;";
      $result = pg_query($db_conn, $query);
      if ($result != false) {
        while ($rows = pg_fetch_array($result)) {
          echo "<tr>";
          echo "<td>" . $rows["genome_id"] . "</td>";
          echo '<td>' . $rows["sequence_id"] . '</td>';
          # Review annotation
          echo '<td> <input type="button" class="button_active" value="annotate" onclick="location.href=\'sequence_annotation.php?gid=' . $rows['genome_id'] . '&sid=' . $rows["sequence_id"] .  '\';"/></td>';
          echo "</tr>";
        }
      } else {
        echo "
        <tr>
        <td colspan='3'>Something went wrong with the query</td>
        </tr>
    ";
      }
      ?>
    </tbody>
  </table>

</div>



<br> Sequences already annotated
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
        <th>Génomes</th>
        <th>Sequences</th>
        <th>Validator's comment</th>
        <th>Annotation status</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $query = "SELECT a.genome_id, a.sequence_id, a.comments, a.status
      FROM annotation_seq.annotations a
      WHERE a.annotator ='" . $_SESSION['user'] . "' and a.status is not null;";
      $result = pg_query($db_conn, $query);
      if ($result != false) {
        while ($rows = pg_fetch_array($result)) {
          echo "<tr>";
          echo "<td>" . $rows["genome_id"] . "</td>";
          echo '<td>' . $rows["sequence_id"] . '</td>';
          # Review annotation
          echo "<td>" . $rows["comments"] . "</td>";
          echo '<td>' . $rows["status"] . '</td>';
          echo "</tr>";
        }
      } else {
        echo "
        <tr>
        <td colspan='3'>Something went wrong with the query</td>
        </tr>
    ";
      }
      ?>
    </tbody>
  </table>

</div>

</body>

</html>