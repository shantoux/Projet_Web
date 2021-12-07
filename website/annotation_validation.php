<!-- Web page to validate or refuse a sequence's annotations -->
<?php session_start();
include_once 'libphp/dbutils.php';
connect_db(); ?>

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
      echo "<a href=\"./forum.php\">Forum</a>";
    }
    if ($_SESSION['role'] == 'administrator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a class=\"active\" href=\"./annotation_validation.php\">Validate annotation</a>";
      echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
      echo "<a href=\"./user_list.php\">Users' List</a>";
    }
    ?>
    <a href="about.php">About</a>
    <a class="disc" href="disconnect.php">Disconnect</a>
  </div>

  <h2 id="pagetitle"> Annotations waiting for validation </h2>

  <?php

  //----------------------------------------------------------------------------------------------------------
  //                                        Actions of the validator 
  //          The validator either accepts this attempt of the sequence's annotation or rejects
  //                                it and assigns the annotator a new attempt
  //----------------------------------------------------------------------------------------------------------


  //------------------------------The validator accepts the annotation with a comment -------------------------

  if (isset($_POST['accept_button'])) {

    //Retrieve last attempt number by a query getting the attempt's number with the waiting status (last attempt) :
    $query_attempt = "SELECT a.attempt 
      FROM database_projet.annotations a 
      WHERE genome_id = '" . $_GET['gid'] . "' AND sequence_id = '" . $_GET['sid'] . "' AND status= 'waiting';";
    $result_attempt = pg_query($db_conn, $query_attempt) or die('Query failed with exception: ' . pg_last_error());
    $attempt = pg_fetch_result($result_attempt, 0, 0);

    //Retrieve value of comment, genome_id and sequence_id of the reviewed annotation :
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";
    $genome_id = $_GET['gid'];
    $sequence_id = $_GET['sid'];

    //Updating the status of the annotation to 'validated' by the validator
    $query = "UPDATE database_projet.annotations
              SET status = 'validated',
              comments = " . $comments .
      " WHERE sequence_id =" . $sequence_id .
      " AND attempt = " . $attempt . ";";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    //----------------Send an email to the annotator, informing them of the decision
    if ($result) {
      echo "Annotation validated. An email was sent to the annotator.";

      $to = $_GET["annotator"]; // Send email to the annotator
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
      $headers[] = "X-Mailer: PHP/" . phpversion(); // Set from headers

      mail($to, $subject, $emessage, implode("\r\n", $headers));
    } else {
      echo "something went wrong in the query";
    }

//------------------------------The validator rejects the annotation with a comment-------------------------------
 
  } else if (isset($_POST['reject_button'])) {
    //Retrieve value of comment, genome_id, sequence_id of the reviewed annotation and annotator of last attempt:
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";
    $genome_id = $_GET['gid'];
    $sequence_id = $_GET['sid'];
    $annotator = $_GET['annotator'];

    //Retrieve last attempt number :
    $query_attempt = "SELECT a.attempt
      FROM database_projet.annotations a 
      WHERE genome_id = '" . $genome_id . "' AND sequence_id = '" . $sequence_id . "' AND status='waiting';";
    $result_attempt = pg_query($db_conn, $query_attempt) or die('Query failed with exception: ' . pg_last_error());
    $attempt = pg_fetch_result($result_attempt, 0, 0);

    //Set this attempt's status to 'rejected'
    $query = "UPDATE database_projet.annotations
              SET status = 'rejected',
              comments = " . $comments .
      " WHERE sequence_id =" . $sequence_id .
      " AND attempt = " . $attempt . ";";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    //Retrieve informations to add a new attempt to that sequence's annotation
    $values_attempt = array();
    $values_attempt['genome_id'] = $genome_id;
    $values_attempt['sequence_id'] = $sequence_id;
    $values_attempt['annotator'] = $annotator;
    $values_attempt['attempt'] = $attempt + 1; //Incrementation of the attempt's number

    $result_insert = pg_insert($db_conn, 'database_projet.annotations', $values_attempt) or die('Query failed with exception: ' . pg_last_error());;

    if ($result and $result_insert) {
      echo "Annotation successfully rejected -_-";

    //----------------Send an email to the annotator, informing them of the decision


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
      $headers[] = "X-Mailer: PHP/" . phpversion(); // Set from headers

      mail($to, $subject, $emessage, implode("\r\n", $headers));
    } else {
      echo "something went wrong in the query";
    }
  }
  ?>

<!------------------------------------------------------------------------------------------------------------
  //                      Display of the list of annotations to be validated by the validator, after being 
  //                                 annotated by the annotator in charge of this sequence
  //------------------------------------------------------------------------------------------------------------->


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

        //Postgres query to get all the sequences that have a status of "waiting" in the annotations table, after annotation
        $query = "SELECT a.genome_id, a.sequence_id, a. comments, a.annotator FROM database_projet.annotations as a WHERE status = 'waiting';";
        $result = pg_query($db_conn, $query);
        if ($result != false) { //If the query succeeded

          //Display by a table all the attempts of annotations waiting to be validated
          while ($rows = pg_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $rows["genome_id"] . "</td>";
            echo '<td><a href="./sequence_annotation.php?gid=' . $rows['genome_id'] . '&sid=' . $rows['sequence_id'] . '">' . $rows["sequence_id"] . '</a></td>';
            echo "<td>" . $rows["annotator"] . "</td>";
            //The form returns to the same page, with the sequence_id and the genome_id in the url if a submit button in pressed (cf actions of the validator)
            echo '<td> <form action="annotation_validation.php?gid=' . $rows['genome_id'] .'&sid=' . $rows["sequence_id"] . '&annotator=' . $rows["annotator"] . '" method = "post">';
            echo "<textarea id=\"" . $rows["sequence_id"] . "\" name=\"comments\" cols=\"40\" rows=\"3\" required>" . $rows['comments'] . "</textarea></td>";            # Validate / Refuse annotation
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




</body>

</html>