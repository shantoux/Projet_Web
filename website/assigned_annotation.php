<!-- Web page to annotate sequences -->
<?php session_start(); ?>

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
  if ($_SESSION['role'] == 'annotator') {
    echo "<a class=\"active\" href=\"./assigned_annotation.php\">Annotate sequence</a>";
    echo "<a href=\"./forum.php\">Forum</a>";
  }
  if ($_SESSION['role'] == 'validator') {
    echo "<a class=\"active\" href=\"./assigned_annotation.php\">Annotate sequence</a>";
    echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
    echo "<a href=\"./forum.php\">Forum</a>";
  }
  if ($_SESSION['role'] == 'administrator') {
    echo "<a class=\"active\" href=\"./assigned_annotation.php\">Annotate sequence</a>";
    echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
    echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
    echo "<a href=\"./forum.php\">Forum</a>";
    echo "<a href=\"./user_list.php\">Users' List</a>";
  }
  ?>
  <a href="about.php">About</a>
  <a class="disc" href="disconnect.php">Disconnect</a>
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
        WHERE a.annotator ='" . $_SESSION['user'] . "' and a.status is null;";
      $result = pg_query($db_conn, $query);
      if ($result != false) {
        while ($rows = pg_fetch_array($result)) {
          echo "<tr>";
          echo "<td>" . $rows["genome_id"] . "</td>";
          echo '<td>' . $rows["sequence_id"] . '</td>';
          # Review annotation
          echo '<td> <input type="button" class="button_active" value="annotate" onclick="location.href=\'sequence_annotation.php?gid=' . $rows['genome_id'] . '&sid=' . $rows["sequence_id"] .  '\';"/></td>';
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
      $query = "SELECT a.genome_id, a.sequence_id, a.comments, a.status
      FROM database_projet.annotations a
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

</body>

</html>
