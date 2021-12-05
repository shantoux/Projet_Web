<!-- Web page to validate sequence annotations -->
<?php session_start(); ?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Annotation validation </title>
  <link rel="stylesheet" type="text/css" href="./pw_style.css" />
</head>

<body class="center">

  <!-- display menu options depending of the user's role -->
  <div class="topnav">
    <a href="./search_1.php">New search</a>
    <?php
    if ($_SESSION['status'] == 'validator') {
      echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
      echo "<a class=\"active\" href=\"./validation_1.php\">Validate annotation</a>";
    }
    if ($_SESSION['status'] == 'administrator') {
      echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
      echo "<a class=\"active\" href=\"./validation_1.php\">Validate annotation</a>";
      echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
    }
    ?>
    <a href="about.php">About</a>
    <a class="disc" href="Login_page1.php">Disconnect</a>
  </div>

  <h2 id="pagetitle"> Annotations waiting for validation </h2>

  <!-- TODO: retrieve anotations from the database. The following is hardcoded data to display pages in the meantime. -->
  <div class="table_type1">
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
          <th>Génomes</th>
          <th>Sequences</th>
          <th>Annotator</th>
          <th>Comments</th>
          <th colspan=2>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php
        include_once 'libphp/dbutils.php';
        connect_db();
        $query = "SELECT a.genome_id, a.sequence_id, a.annotator FROM annotation_seq.annotations as a WHERE status = 'waiting';";
        $result = pg_query($db_conn, $query);
        if ($result != false) {
          while ($rows = pg_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $rows["genome_id"] . "</td>";
            echo "<td>" . $rows["sequence_ic"] . "</td>";
            echo "<td>" . $rows["annotator"] . "</td>";
            # Review annotation
            echo "<td> <form action=\"annotation_1.php\" method = \"post\">";
            echo "<input type=\"submit\" value=\"Review annotation\" name=\"review\" id=" . $row['id'] . "</form> </td>";
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
      <tbody>
    </table>
    <?php

    //Ici faire le résultat du submit
    ?>
  </div>

</body>

</html>