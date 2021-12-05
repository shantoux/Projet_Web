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
        $query = "SELECT a.genome_id, a.sequence_id, a. comments, a.annotator FROM annotation_seq.annotations as a WHERE status = 'waiting';";
        $result = pg_query($db_conn, $query);
        if ($result != false) {
          while ($rows = pg_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $rows["genome_id"] . "</td>";
            echo "<td><a href=\"./sequence_annotation.php?id=" . $rows["sequence_id"] . "\">" . $rows["sequence_id"] . "</a></td>";
            echo "<td>" . $rows["annotator"] . "</td>";
            # Review annotation
            echo "<td> <form action=\"validation_1.php\" method = \"post\">";
            echo "<textarea id=" . $rows['comments'] . "name=\"comments\" cols=\"40\" rows=\"3\" >" . $rows['comments'] . "</textarea></td>";            # Validate / Refuse annotation
            echo "<td>";
            echo "<div style=\"float:left; width: 50%;\">";
            echo "<button type=\"submit\" name=\"accept_button\" value=" . $rows['sequence_id'] . ">accept</button></div>";
            echo "<div style=\"float: left; width: auto;\">";
            echo "<button type=\"submit\" name=\"reject_button\" value=" . $rows['sequence_id'] . ">reject</button></div>";
            echo "</td>";
            echo "</form>";
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

  </div>
  <?php
  
  //Ici faire le résultat du submit
  if (isset($_POST['accept_button'])) {
    //Retrieve value of comment :
    $comments = htmlspecialchars($_POST['comments']);
    $sequence_id = $_POST['accept_button'];
    //Query on postgres
    $query = "UPDATE annotation_seq.annotations
                SET status = 'validated'
                SET comments = " . $comments .
      "WHERE sequence_id =" . $sequence_id . ";";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
    if ($result) {
      echo "Annotation validated :)";
    } else {
      echo "something went wrong in the query";
    }
  } else if (isset($_POST['reject_button'])) {
    //Retrieve value of comment :
    $comments = htmlspecialchars($_POST['comments']);
    echo $comments;
    $sequence_id = $_POST['accept_button'];
    echo $sequence_id;
    //Query on postgres
    $query = "UPDATE annotation_seq.annotations
                SET status = 'rejected'
                SET comments = " . $comments .
      "WHERE sequence_id =" . $sequence_id . ";";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
    if ($result) {
      echo "Annotation successfully rejected -_-";
    } else {
      echo "something went wrong in the query";
    }
  }
  ?>



</body>

</html>