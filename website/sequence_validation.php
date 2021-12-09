<!-- Web page to get information about gene or protein -->
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
  <title>Annotation </title>
  <link rel="stylesheet" type="text/css" href="./style.css" /s>
  <script type="text/javascript" src="function.js"></script>
</head>

<body>
  <!-- display menu options depending of the user's role -->
  <div class="topnav">
    <a href="./search.php">New search</a>
    <?php
    if ($_SESSION['role'] == 'Annotator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
    }
    if ($_SESSION['role'] == 'Validator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
      echo "<a href=\"./consult_annotation.php\">Consult</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
    }
    if ($_SESSION['role'] == 'Administrator') {
      echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
      echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
      echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
      echo "<a href=\"./consult_annotation.php\">Consult</a>";
      echo "<a href=\"./forum.php\">Forum</a>";
      echo "<a href=\"./user_list.php\">Users' List</a>";
    }
    ?>
    <a href="about.php">About</a>
    <a class="disc" href="login.php">Disconnect</a>
    <a class="disc"><?php echo $_SESSION['first_name'] ?> - <?php echo $_SESSION['role'] ?> </a>
  </div>

  <h2 id="pagetitle">
    Sequence Annotation Validation
  </h2>

  <?php

  include_once 'libphp/dbutils.php';
  connect_db();
  // Retrieve the information already in the database
  $genome_id = $_GET['gid'];
  $sequence_id = $_GET['sid'];
  $attempt = $_GET['att'];
  $annotator = $_GET['annotator'];

  $query2 = "SELECT g.gene_seq, g.prot_seq, g.start_seq, g.end_seq, g.chromosome
    FROM database_projet.gene g
    WHERE g.sequence_id = '" . $sequence_id . "';";
  $result2 = pg_query($db_conn, $query2) or die('Query failed with exception: ' . pg_last_error());
  $nt = pg_fetch_result($result2, 0, 0);
  $prot = pg_fetch_result($result2, 0, 1);
  $start = pg_fetch_result($result2, 0, 2);
  $end = pg_fetch_result($result2, 0, 3);
  $chromosome = pg_fetch_result($result2, 0, 4);

  ?>

  <?php



  //Retrieve status of sequence annotation
  $query_infos = "SELECT a.status, a.gene_id, a.gene_biotype, a.transcript_biotype, a.gene_symbol, a.description
  FROM database_projet.annotations a
  WHERE sequence_id = '" . $_GET['sid'] . "' AND attempt =" . $attempt . " AND annotator ='".$annotator."' ;";
  $result_info = pg_query($db_conn, $query_infos) or die('Query failed with exception: ' . pg_last_error());
  $status = pg_fetch_result($result_info, 0, 0);
  $gene_id = pg_fetch_result($result_info, 0, 1);
  $gene_biotype = pg_fetch_result($result_info, 0, 2);
  $transcript_biotype = pg_fetch_result($result_info, 0, 3);
  $gene_symbol = pg_fetch_result($result_info, 0, 4);
  $description = pg_fetch_result($result_info, 0, 5);
  ?>


<?php
  if (isset($_POST['validate_annotation'])) {

    //Retrieve value of comment, genome_id and sequence_id of the reviewed annotation :
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";

    //Updating the status of the annotation to 'validated' by the validator
    $query = "UPDATE database_projet.annotations
              SET status = 'validated',
              comments = " . $comments .
      " WHERE sequence_id ='" . $sequence_id .
      "' AND attempt = " . $attempt . " AND annotator ='".$annotator."';";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    //----------------Send an email to the annotator, informing them of the decision
    if ($result) {
      echo "Annotation validated. An email was sent to the annotator.";

      $to = $_GET["annotator"]; // Send email to the annotator
      $subject = "Your annotation has been validated."; // Give the email a subject
      $emessage = "Your annotation has been validated. \r\n Thank you for your contribution. \r\n The validator's comment :  ".$comments."";

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
      echo "something went wrong in the query";
    }

//------------------------------The validator rejects the annotation with a comment-------------------------------

} else if (isset($_POST['reject_annotation'])) {
    //Retrieve value of comment, genome_id, sequence_id of the reviewed annotation and annotator of last attempt:
    $comments = "'" . htmlspecialchars($_POST["comments"], ENT_QUOTES) . "'";

    //Set this attempt's status to 'rejected'
    $query = "UPDATE database_projet.annotations
              SET status = 'rejected',
              comments = " . $comments .
      " WHERE sequence_id ='" . $sequence_id .
      "' AND attempt = " . $attempt . " AND annotator='" .$annotator."';";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    //Retrieve informations to add a new attempt to that sequence's annotation
    $values_attempt = array();
    $values_attempt['genome_id'] = $genome_id;
    $values_attempt['sequence_id'] = $sequence_id;
    $values_attempt['annotator'] = $annotator;
    $values_attempt['attempt'] = $attempt + 1; //Incrementation of the attempt's number
    $values_attempt['status'] = 'assigned';

    $result_insert = pg_insert($db_conn, 'database_projet.annotations', $values_attempt) or die('Query failed with exception: ' . pg_last_error());

    if ($result and $result_insert) {
      echo "Annotation successfully rejected. Please go back to validation page";

    //----------------Send an email to the annotator, informing them of the decision


      $to = $_GET["annotator"]; // Send email to our user
      $subject = "Your annotation has been rejected."; // Give the email a subject
      $emessage = "Your annotation has been rejected \r\n Please review the validator's comment and submit another annotation. \r\n The validator's comment :  ".$comments." ";

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
      echo "something went wrong in the query";
    }
  }
  ?>

  <div class="center">


    <table class="table_type3">
      <tr colspan=2>
        <td>
          <b>Sequence identifier:</b> <?php echo $sequence_id; ?><br><br>
          <b>Specie:</b> <?php echo $genome_id; ?><br>
          <b>Chromosome:</b> <?php echo $chromosome; ?><br>
          <?php echo 'Sequence is ' . strlen($nt) . ' nucleotides long <br>it starts on position <b>' . $start . '</b> and ends on position <b>' . $end . '</b>.<br><br>'; ?>


    <?php if ($status == 'waiting') : ?>
      <!-- display gene biotype -->
      <b>Gene identifier: </b> <?php echo $gene_id ?> <br><br>

      <!-- display transcript biotype -->
      <b>Gene biotype: </b> <?php echo $gene_biotype ?> <br><br>

      <!-- display transcript biotype -->
      <b>Transcript biotype: </b> <?php echo $transcript_biotype ?> <br><br>

      <!-- display gene symbol -->
      <b>Gene symbol: </b> <?php echo $gene_symbol ?> <br><br>

      <!-- display description -->
      <b>Description: </b> <?php echo $description ?> <br><br>
      </td><td>
      <?php if ($_SESSION['role'] == ('Validator' ||'Administrator') && $_SESSION['user']!=$annotator) : ?>
        <form action="./sequence_validation.php?gid=<?php echo $genome_id ?>&sid=<?php echo $sequence_id ?>&att=<?php echo $attempt?>&annotator=<?php echo $annotator?>" method="post">
          <tr>
            <td>
              Comment to validate or reject <br>
              <textarea name="comments" cols="40" rows="3" required></textarea> <br>
               <input type="submit" value="Validate" name="validate_annotation">
            <input type="submit" value="Reject" name="reject_annotation">
            </td><td>
      </tr>
        </form>
        <?php endif;?>
      </tr>
      <tr></tr>
      <tr>
        <td>
          Gene sequence<br>
          <textarea id="seq" name="seq" rows="8" cols="80" readonly><?php echo $nt ?></textarea>
        </td>

        <td>
          <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $nt . "&type=nucl\" target=\"_blank\">" ?>
          <button type="button">Align with Blast</button>
          </a>
        </td>
      </tr>

      <tr>
        <td>
          Peptide sequence<br>
          <textarea id="seq" name="seq" rows="8" cols="80" readonly><?php echo $prot; ?> </textarea>
        </td>
        <td>
          <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $prot . "&type=prot\" target=\"_blank\">" ?>
          <button type="button">Align with Blast</button>
          </a>
      </tr>

    <?php endif; ?>


    </table>
  </div>


  <h3 id="pageundertitle" class="center"> Past attempts </h3>
  <div id="element1">
    <?php
    $query_pastattempts = "SELECT a.attempt, a.gene_id, a.gene_biotype, a.transcript_biotype, a.gene_symbol, a.description, a.comments, a.status
      FROM database_projet.annotations as a
      WHERE sequence_id ='" . $sequence_id . "'and status = 'rejected'
      ORDER BY attempt DESC;";
    $result_attempts = pg_query($db_conn, $query_pastattempts);
    if (pg_num_rows($result_attempts) > 0) {
      echo '<table class="table_type1">';
      echo '<colgroup>';
      echo '<col style="width: 10%"><col style="width: 10%"><col style="width: 10%"><col style="width: 10%">';
      echo '</colgroup>';
      echo '<thead>';
      echo '<tr>';
      echo '<th>Attempt</th><th>Gene id</th><th>gene biotype</th><th>transcript_biotype</th><th>gene_symbol</th><th>description</th><th>Validator\'s comment</th>';
      echo '</tr>';
      echo '</thead>';
      echo ' <tbody>';

      while ($rows = pg_fetch_array($result_attempts)) {
        echo "<tr>";
        echo "<td>" . $rows["attempt"] . "</td>";
        echo '<td>' . $rows["gene_id"] . '</td>';
        echo '<td>' . $rows["gene_biotype"] . '</td>';
        echo '<td>' . $rows["transcript_biotype"] . '</td>';
        echo '<td>' . $rows["gene_symbol"] . '</td>';
        echo '<td>' . $rows["description"] . '</td>';
        echo "<td>" . $rows["comments"] . "</td>";
        echo "</tr>";
      }
    } else {
      echo "This is the first attempt";
    }

    echo '</tbody>';
    echo '</table>';
    ?>

  </div>

</body>

</html>
