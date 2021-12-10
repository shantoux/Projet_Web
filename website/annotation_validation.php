<!-- Web page to validate or refuse a sequence's annotations -->
<?php session_start();

// check if user is logged in: else, redirect to login page
if (!isset($_SESSION['user'])) {
  echo '<script>location.href="login.php"</script>';
}
// import db functions
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
    if ($_SESSION['role'] == 'Validator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a class=\"active\" href=\"./annotation_validation.php\">Validate annotation</a>";
      echo "<a href=\"./consult_annotation.php\">Consult</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
    }
    if ($_SESSION['role'] == 'Administrator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a class=\"active\" href=\"./annotation_validation.php\">Validate annotation</a>";
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

  <!-- Display page title -->
  <h2 id="pagetitle"> Annotations waiting for validation </h2>



  <!-- ////////////////////////////////////////////////////////////////////////////////////////////
  //                                        Actions of the validator                             //
  //          The validator either accepts this attempt of the sequence's annotation or rejects  //
  //                                it and assigns the annotator a new attempt                  //
  ////////////////////////////////////////////////////////////////////////////////////////////////


  /////The validator accepts the annotation with a comment -->
  <?php
  if (isset($_POST['Accept_button'])) {

    //Retrieve value of comment, genome_id and sequence_id of the reviewed annotation :
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";
    $genome_id = $_GET['gid'];
    $sequence_id = $_GET['sid'];
    $annotator = $_GET['annotator'];
    $attempt = $_GET['att'];

    //Updating the status of the annotation to 'validated' by the validator
    $query = "UPDATE database_projet.annotations
              SET status = 'validated',
              comments = " . $comments ."
              WHERE sequence_id ='" . $sequence_id ."'
              AND attempt = " . $attempt . " AND annotator = '" . $annotator . "';";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    //----------------Send an email to the annotator, informing them of the decision
    if ($result) {
      echo "<br> <div class=\"alert_good\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Annotation <b>validated</b>. <br>An email was sent to the annotator.
        </div>";

      $to = $_GET["annotator"]; // Send email to the annotator
      $subject = "Your annotation has been validated."; // Give the email a subject
      $emessage = "Your annotation has been validated. \r\n Thank you for your contribution. \r\n The validator's comment :  " . $comments . "";

      // if emessage is more than 70 chars
      $emessage = wordwrap($emessage, 70, "\r\n");

      // Our emessage above including the link
      $headers   = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/plain; charset=iso-8859-1";
      $headers[] = "From: Bio Search Sequences <noreply@yourdomain.com>";
      $headers[] = "Subject: {$subject}";
      $headers[] = "X-Mailer: PHP/" . phpversion(); // Set from headers

      mail($to, $subject, $emessage, implode("\r\n", $headers));
    } else {
      echo "Something went wrong, please contact the administrator to inform them";
    }

    //------------------------------The validator rejects the annotation with a comment-------------------------------

  } else if (isset($_POST['Reject_button'])) {
    //Retrieve value of comment, genome_id, sequence_id of the reviewed annotation and annotator of last attempt:
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";
    $genome_id = $_GET['gid'];
    $sequence_id = $_GET['sid'];
    $annotator = $_GET['annotator'];
    $attempt = $_GET['att'];

    //Set this attempt's status to 'rejected'
    $query = "UPDATE database_projet.annotations
              SET status = 'rejected',
              comments = " . $comments .
      " WHERE sequence_id ='" . $sequence_id .
      "' AND attempt = " . $attempt . " AND annotator = '" . $annotator . "';";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    //Retrieve informations to add a new attempt to that sequence's annotation
    $values_attempt = array();
    $values_attempt['genome_id'] = $genome_id;
    $values_attempt['sequence_id'] = $sequence_id;
    $values_attempt['annotator'] = $annotator;
    $values_attempt['attempt'] = $attempt + 1; //Incrementation of the attempt's number
    $values_attempt['status'] = 'assigned';

    $result_insert = pg_insert($db_conn, 'database_projet.annotations', $values_attempt) or die('Query failed with exception: ' . pg_last_error());;

    if ($result and $result_insert) {
      echo "<br> <div class=\"alert_good\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Annotation successfully <b>rejected</b>.
        </div>";

      //----------------Send an email to the annotator, informing them of the decision


      $to = $_GET["annotator"]; // Send email to our user
      $subject = "Your annotation has been rejected."; // Give the email a subject
      $emessage = "Your annotation has been rejected \r\n Please review the validator's comment and submit another annotation. \r\n The validator's comment :  " . $comments . " ";

      // if emessage is more than 70 chars
      $emessage = wordwrap($emessage, 70, "\r\n");

      // Our emessage above including the link
      $headers   = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/plain; charset=iso-8859-1";
      $headers[] = "From: Bio Search Sequences <noreply@yourdomain.com>";
      $headers[] = "Subject: {$subject}";
      $headers[] = "X-Mailer: PHP/" . phpversion(); // Set from headers

      mail($to, $subject, $emessage, implode("\r\n", $headers));
    } else {
      echo "Something went wrong, please contact the administrator to inform them";
    }
  }
  ?>

  <!--///////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                      Display of the list of annotations to be validated by the validator, after being
  //                                 annotated by the annotator in charge of this sequence
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
  <div id="element1">
    <table class="table_type1">

      <?php

      //Query to get all the sequences that have a status of "waiting" in the annotations table, after annotation
      $query = "SELECT a.genome_id, a.sequence_id, a. comments, a.annotator, a.attempt, a.assignation_date
      FROM database_projet.annotations as a
      WHERE status = 'waiting' AND annotator != '" . $_SESSION['user'] . "';";
      $result = pg_query($db_conn, $query);

      if (pg_num_rows($result) > 0) {
        //If there are annotations to validate, display the table
        echo "<thead>";
        echo "<tr>";
        echo "<th>Génomes</th>";
        echo "<th>Sequences</th>";
        echo "<th>Annotator</th>";
        echo "<th>Attempt number</th>";
        echo "<th>(Re)assignation date</th>";
        echo "<th>Comments</th>";
        echo "<th>Action</th>";
        echo "</tr>";
        echo "</thead><tbody>";

        if ($result != false) { //If the query succeeded

          //Display a table with all the attempts of annotations waiting to be validated
          while ($rows = pg_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $rows["genome_id"] . "</td>";
            echo '<td><a href="./sequence_validation.php?gid=' . $rows['genome_id'] . '&sid=' . $rows['sequence_id'] . '&att=' . $rows['attempt'] . '&annotator=' . $rows["annotator"] . '">' . $rows["sequence_id"] . '</a></td>';
            echo "<td>" . $rows["annotator"] . "</td>";
            echo "<td>" . $rows["attempt"] . "</td>";
            echo "<td>" . date('d-m-o H:i', strtotime($rows['assignation_date'])) . "</td>";
            //The form returns to the same page, with the sequence_id and the genome_id in the url if a submit button in pressed (cf actions of the validator)
            echo '<td> <form action="annotation_validation.php?gid=' . $rows['genome_id'] . '&sid=' . $rows["sequence_id"] . '&att=' . $rows['attempt'] . '&annotator=' . $rows["annotator"] . '" method = "post">';
            echo "<textarea id=\"" . $rows["sequence_id"] . "\" name=\"comments\" cols=\"40\" rows=\"3\" required>" . $rows['comments'] . "</textarea></td>";            # Validate / Refuse annotation
            echo "<td>";
            echo '<input class="button_ok" type="submit" name="Accept_button" value="Accept">';
            echo '<input class="button_red" type="submit" name="Reject_button" value="Reject"> </form>';
            echo "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='3'>
            Something went wrong with the query</td></tr>";
        }
      } else {
        # display message
        echo "<div class=\"alert_neutral\">
          Wait for an annotator to submit their work.</div>";
      }

      echo "</tbody>";
      ?>

    </table>
  </div>
</body>

</html>
