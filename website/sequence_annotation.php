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
</head>

<!-- display menu options depending of the user's role -->
<body class="center">
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
      echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
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

  <!-- Display page title -->
  <h2 id="pagetitle">
    Sequence Annotation
  </h2>

  <?php
  // import db functions
  include_once 'libphp/dbutils.php';
  connect_db();

  // Retrieve the information already in the database
  $genome_id = $_GET['gid'];
  $sequence_id = $_GET['sid'];
  $attempt = $_GET['att'];
  $annotator = $_GET['annotator'];

  //Query to retrieve information about the sequence to annotate
  $query2 = "SELECT g.gene_seq, g.prot_seq, g.start_seq, g.end_seq, g.chromosome
      FROM database_projet.gene g
      WHERE g.sequence_id = '" . $sequence_id . "';";
  $result2 = pg_query($db_conn, $query2) or die('Query failed with exception: ' . pg_last_error());

  $nt = pg_fetch_result($result2, 0, 0);
  $prot = pg_fetch_result($result2, 0, 1);
  $start = pg_fetch_result($result2, 0, 2);
  $end = pg_fetch_result($result2, 0, 3);
  $chromosome = pg_fetch_result($result2, 0, 4);



  if (isset($_POST['send_annotation']) || isset($_POST['save_annotation'])) {
    //Retrieve informations from form
    $values_annotations = array();
    $values_annotations['gene_id'] = $_POST["gene_id"];
    $values_annotations['gene_biotype'] = $_POST["gene_biotype"];
    $values_annotations['transcript_biotype'] = $_POST["transcript_biotype"];
    $values_annotations['gene_symbol'] = $_POST["gene_symbol"];
    $values_annotations['description'] = $_POST["description"];
    if (isset($_POST['send_annotation'])) {
      $values_annotations['status'] = 'waiting';
    } else if (isset($_POST['save_annotation'])) {
      $values_annotations['status'] = 'assigned';
    }

    //Conditions for query

    $condition_pkey = array();
    $condition_pkey['genome_id'] = $genome_id;
    $condition_pkey['sequence_id'] = $sequence_id;
    $condition_pkey['attempt'] = $attempt;
    $condition_pkey['annotator'] = $annotator; //$_GET['annotator'];

    //Update database
    $result_update = pg_update($db_conn, 'database_projet.annotations', $values_annotations, $condition_pkey)
      or die('Query failed with exception: ' . pg_last_error());

    if ($result_update) {
      if (isset($_POST['send_annotation'])) {
        echo "<br> <div class=\"alert_good\">
            <span class=\"closebtn\"
            onclick=\"this.parentElement.style.display='none';\">&times;</span>
            Annotation <b>sent</b> ! Redirection to Annotate Sequence page ...
          </div>";
        echo '<meta http-equiv = "refresh" content = " 2 ; url = assigned_annotation.php"/>';


        //echo '<meta http-equiv = "refresh" content = " 0 ; url = ./sequence_validation.php?gid=' . $genome_id . '&sid=' . $sequence_id . '&att=' . $attempt . '&annotator=' . $annotator . '"/>';
      } else if (isset($_POST['save_annotation'])) {
        echo "<br> <div class=\"alert_good\">
            <span class=\"closebtn\"
            onclick=\"this.parentElement.style.display='none';\">&times;</span>
            Annotation <b>saved</b> ! Redirection to Annotate Sequence page ...
          </div>";
        echo '<meta http-equiv = "refresh" content = " 2 ; url = assigned_annotation.php"/>';
      }
    }
  }



  //Retrieve status of sequence annotation
  $query_infos = "SELECT a.status, a.gene_id, a.gene_biotype, a.transcript_biotype, a.gene_symbol, a.description, a.annotator
    FROM database_projet.annotations a
    WHERE sequence_id ='" . $sequence_id .
    "' AND attempt = " . $attempt . " AND annotator = '" . $annotator . "';";
  $result_info = pg_query($db_conn, $query_infos) or die('Query failed with exception: ' . pg_last_error());

  $status = pg_fetch_result($result_info, 0, 0);
  $gene_id = pg_fetch_result($result_info, 0, 1);
  $gene_biotype = pg_fetch_result($result_info, 0, 2);
  $transcript_biotype = pg_fetch_result($result_info, 0, 3);
  $gene_symbol = pg_fetch_result($result_info, 0, 4);
  $description = pg_fetch_result($result_info, 0, 5);
  $annotator = pg_fetch_result($result_info, 0, 6);
  ?>


  <div class="center">

    <table class="table_type3">
      <tr colspan=2>
        <td>
          <b>Sequence identifier:</b> <?php echo $sequence_id; ?><br><br>
          <b>Specie:</b> <?php echo $genome_id; ?><br>
          <b>Chromosome:</b> <?php echo $chromosome; ?><br>
          <?php echo 'Sequence is ' . strlen($nt) . ' nucleotides long - it starts on position <b>' . $start . '</b> and ends on position <b>' . $end . '</b>.<br><br>'; ?>
          <form action="./sequence_annotation.php?gid=<?php echo $genome_id ?>&sid=<?php echo $sequence_id ?>&att=<?php echo $attempt ?>&annotator=<?php echo $annotator ?>" method="post">

            <?php if ($status == 'assigned') : ?>
              <b>Gene identifier : </b><input type="text" name="gene_id" required value="<?php echo (isset($_POST['gene_id'])) ? htmlspecialchars($_POST['gene_id']) : $gene_id ?>"> <br><br>
              <b>Gene biotype : </b><input type="text" name="transcript_biotype" required value="<?php echo (isset($_POST['gene_biotype'])) ? htmlspecialchars($_POST['gene_biotype']) : $gene_biotype ?>"> <br><br>
              <b>Transcript biotype : </b><input type="text" name="gene_biotype" required value="<?php echo (isset($_POST['transcript_biotype'])) ? htmlspecialchars($_POST['transcript_biotype']) : $gene_biotype ?>"> <br><br>
              <b>Gene symbol : </b><input type="text" name="gene_symbol" required value="<?php echo (isset($_POST['gene_symbol'])) ? htmlspecialchars($_POST['gene_symbol']) : $gene_symbol ?>"> <br><br>
              <b>Description : </b><input type="text" name="description" required size = "40" value="<?php echo (isset($_POST['description'])) ? htmlspecialchars($_POST['description']) : $description ?>"> <br>
            <?php else: ?>
              <!-- display gene identifier -->
              <b>Gene identifier: </b> <?php echo $gene_id ?> <br>

              <!-- display gene biotype -->
              <b>Gene biotype: </b> <?php echo $gene_biotype ?> <br>

              <!-- display transcript biotype -->
              <b>Transcript biotype: </b> <?php echo $transcript_biotype ?> <br>

              <!-- display gene symbol -->
              <b>Gene symbol: </b> <?php echo $gene_symbol ?> <br>

              <!-- display description -->
              <b>Description: </b> <?php echo $description ?> <br>
            <?php endif; ?>

        </td>
      </tr>
      <tr></tr>
      <tr>
        <td>
          Gene sequence<br>
          <textarea id="seq" name="seq" rows="8" cols="80" readonly><?php echo $nt ?></textarea>
        </td>

        <td>
          <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $nt . "&type=nucl\" target=\"_blank\">" ?>
          <button type="button" class="button_neutral">Align with Blast</button>
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
          <button type="button" class="button_neutral">Align with Blast</button>
          </a>
      </tr>

      <tr>
        <?php if ($status == 'assigned') : ?>
          <td align='center'> <input class="button_ok" type="submit" value="Send" name="send_annotation">
            <input class="button_blue" type="submit" value="Save" name="save_annotation">
          </td>
        <?php endif; ?>
      </tr>
      </form>

    </table>
  </div>


  <h3 id="pageundertitle" class="center"> Past attempts </h3>
  <div id="element1">
    <?php
    
    //Query to retrieve information about the annotator's last attempt to annotator
    //the same sequence they are annotating now
    $query_pastattempts = "SELECT a.attempt, a.gene_id, a.gene_biotype, a.transcript_biotype, a.gene_symbol, a.description, a.comments, a.status, a.assignation_date
        FROM database_projet.annotations as a
        WHERE sequence_id ='" . $sequence_id . "'and status = 'rejected'
        ORDER BY attempt DESC;";
    $result_attempts = pg_query($db_conn, $query_pastattempts);

    if (pg_num_rows($result_attempts) > 0) {
      echo '<table class="table_type1">';
      echo '<thead>';
      echo '<tr>';
      echo '<th>(Re)assigned on</th><th>Attempt</th><th>Gene id</th><th>gene biotype</th><th>transcript_biotype</th><th>gene_symbol</th><th>description</th><th>Validator\'s comment</th>';
      echo '</tr>';
      echo '</thead>';
      echo ' <tbody>';

      while ($rows = pg_fetch_array($result_attempts)) {
        echo "<tr>";
        echo "<td>" . date('d-m-o H:i', strtotime($rows["assignation_date"])) . "</td>";
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
