<!-- Web page to validate sequence annotations -->
<?php session_start(); ?>

<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Annotation validation </title>
  <link rel="stylesheet" type="text/css" href="./style.css" />
</head>

<body class="center">

  <!-- display menu options depending of the user's role -->
  <div class="topnav">
    <a href="./search.php">New search</a>
    <?php
    if ($_SESSION['role'] == 'validator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a class=\"active\" href=\"./annotation_validation.php\">Validate annotation</a>";
    }
    if ($_SESSION['role'] == 'administrator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a class=\"active\" href=\"./annotation_validation.php\">Validate annotation</a>";
      echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
      echo "<a href=\"./user_list.php\">Users' List</a>";
    }
    ?>
    <a href="about.php">About</a>
    <a class="disc" href="disconnect.php">Disconnect</a>
  </div>

  <h2 id="pagetitle"> Annotations waiting for validation </h2>



  <div id="element1">
    <table class="table_type1">
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
        $query = "SELECT a.genome_id, a.sequence_id, a. comments, a.annotator FROM database_projet.annotations as a WHERE status = 'waiting';";
        $result = pg_query($db_conn, $query);
        if ($result != false) {
          while ($rows = pg_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $rows["genome_id"] . "</td>";
            echo "<td><a href=\"./sequence_annotation.php?gid='" .$rows['genome_id']. "'&sid='" . $rows['sequence_id'] . "'\">" . $rows["sequence_id"] . "</a></td>";
            echo "<td>" . $rows["annotator"] . "</td>";
            # Review annotation
            echo '<td> <form action="annotation_validation.php?seq=' .$rows["sequence_id"]. '" method = "post">';
            echo "<textarea id=\"" . $rows["sequence_id"] . "\" name=\"comments\" cols=\"40\" rows=\"3\" >" . $rows['comments'] . "</textarea></td>";            # Validate / Refuse annotation
            echo "<td>";
            echo "<div style=\"float:left; width: 50%;\">";
            echo '<input type="submit" name="accept_button" value="accept"></div>';
            echo "<div style=\"float: left; width: auto;\">";
            echo '<input type="submit" name="reject_button" value="reject"> </form> </div>';
            echo "</td>";
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
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";
    $sequence_id = "'".$_GET['seq']."'";
    //Query on postgres
    $query = "UPDATE database_projet.annotations
                SET status = 'validated',
                comments = " . $comments .
      " WHERE sequence_id =" . $sequence_id . ";";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
    if ($result) {
      echo "Annotation validated :)";

      $to = $_POST["adress"]; // Send email to our user
      $subject = "Your annotation has been validated."; // Give the email a subject
      $emessage = "Your annotation has been validated. <br>
      Thank you for your contribution.";

      // if emessage is more than 70 chars
      $emessage = wordwrap($emessage, 70, "\r\n");

      // Our emessage above including the link
      $headers   = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/plain; charset=iso-8859-1";
      $headers[] = "From: no-reply <noreply@yourdomain.com>";
      $headers[] = "Subject: {$subject}";
      $headers[] = "X-Mailer: PHP/".phpversion(); // Set from headers

      mail($to, $subject, $emessage, implode("\r\n", $headers));

    } else {
      echo "something went wrong in the query";
    }
  } else if (isset($_POST['reject_button'])) {
    //Retrieve value of comment :
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";
    $sequence_id = "'".$_GET['seq']."'";
    //Query on postgres
    $query = "UPDATE database_projet.annotations
                SET status = 'rejected',
                comments = ". $comments .
      " WHERE sequence_id =" . $sequence_id . ";";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
    if ($result) {
      echo "Annotation successfully rejected -_-";

      $to = $_POST["adress"]; // Send email to our user
      $subject = "Your annotation has been rejected."; // Give the email a subject
      $emessage = "Your annotation has been rejected <br>
      You can try again next time.";

      // if emessage is more than 70 chars
      $emessage = wordwrap($emessage, 70, "\r\n");

      // Our emessage above including the link
      $headers   = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/plain; charset=iso-8859-1";
      $headers[] = "From: no-reply <noreply@yourdomain.com>";
      $headers[] = "Subject: {$subject}";
      $headers[] = "X-Mailer: PHP/".phpversion(); // Set from headers

      mail($to, $subject, $emessage, implode("\r\n", $headers));
    } else {
      echo "something went wrong in the query";
    }
  }
  ?>



</body>

</html>
