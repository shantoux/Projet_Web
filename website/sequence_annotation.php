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

  <div id="pagetitle">
    Sequence Annotation
  </div>

  <?php

  include_once 'libphp/dbutils.php';
  connect_db();
  // Retrieve the information already in the database
  $genome_id = $_GET['gid'];
  $sequence_id = $_GET['sid'];

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


if (isset($_POST['send_annotation']) || isset($POST['save_annotation'])) {
  //Retrieve informations from form
  $values_annotations = array();
  $values_annotations['gene_id'] = $_POST["gene_id"];
  $values_annotations['gene_biotype'] = $_POST["gene_biotype"];
  $values_annotations['transcript_biotype'] = $_POST["transcript_biotype"];
  $values_annotations['gene_symbol'] = $_POST["gene_symbol"];
  $values_annotations['description'] = $_POST["gene_description"];
  if (isset($_POST['send_annotation'])) {
    $values_annotations['status'] = 'waiting';
  } else if (isset($_POST['save_annotation'])) {
    $values_annotations['status'] = 'assigned';
  }
  //Conditions for query

  $condition_pkey = array();
  $condition_pkey['genome_id'] = $_GET['gid'];
  $condition_pkey['sequence_id'] = $_GET['sid'];
  $condition_pkey['attempt'] = $attempt;
  $condition_pkey['annotator'] = $annotator; //$_GET['annotator'];

  //Update database
  $result_update = pg_update($db_conn, 'database_projet.annotations', $values_annotations, $condition_pkey)
    or die('Query failed with exception: ' . pg_last_error());

  if ($result_update) {
    echo "Annotation has been sent. Wait for validation.";
  } else {
    echo "Error : the annotation has not been sent.";
  }
}

?>

  <div class="center">
    <?php

    /////Retrieve latest attempt number
    $query_attempt = "SELECT a.attempt
      FROM database_projet.annotations a
      WHERE genome_id = '" . $_GET['gid'] . "' AND sequence_id = '" . $_GET['sid'] . "' AND status = 'assigned';";
    $result_attempt = pg_query($db_conn, $query_attempt) or die('Query failed with exception: ' . pg_last_error());
    $attempt = pg_fetch_result($result_attempt, 0, 0);


    //Retrieve status of sequence annotation
    $query_infos = "SELECT a.status, a.gene_id, a.gene_biotype, a.transcript_biotype, a.gene_symbol, a.description, a.annotator
      FROM database_projet.annotations a
      WHERE sequence_id = '" . $_GET['sid'] . "' AND attempt ='" . $attempt . "' ;";
    $result_info = pg_query($db_conn, $query_infos) or die('Query failed with exception: ' . pg_last_error());
    $status = pg_fetch_result($result_info, 0, 0);
    $gene_id = pg_fetch_result($result_info, 0, 1);
    $gene_biotype = pg_fetch_result($result_info, 0, 2);
    $transcript_biotype = pg_fetch_result($result_info, 0, 3);
    $gene_symbol = pg_fetch_result($result_info, 0, 4);
    $description = pg_fetch_result($result_info, 0, 5);
    $annotator = pg_fetch_result($result_info, 0, 6);

    echo '<table class="table_type3">';
    echo '<tr colspan=2>';
    echo '<td>';
    echo "<b>Sequence identifier:</b> $sequence_id<br><br>";
    echo "<b>Specie:</b> $genome_id<br>";
    echo "<b>Chromosome:</b> $chromosome<br>";
    echo "Sequence is " . strlen($nt) . " nucleotides long - it starts on position <b>" . $start . "</b> and ends on position <b>" . $end . "</b>.<br><br>";

    if ($status == 'assigned'){
      echo '<form action="./sequence_annotation.php?gid=' . $genome_id . '&sid=' . $sequence_id . '" method="post">';
      echo '<b>Gene identifier : </b><input type="text" name="gene_id" value ="'. $gene_id .'"> <br>';
      echo '<b>Gene biotype : </b><input type="text" name="gene_biotype"><br>';
      echo '<b>Transcript biotype : </b><input type="text" name="transcript_biotype"><br>';
      echo '<b> Gene symbol : </b><input type ="text" name = "gene_symbol"><br>';
      echo '<b> Description : </b><input type ="text" name = "gene_description"><br>';
      echo '</form>';
      echo '</td>';
    } 
    else if ($status == 'waiting') {
      echo '<form action="./sequence_annotation.php?gid=' . $genome_id . '&sid=' . $sequence_id . '" method="post">';
      echo '<b>Gene identifier : </b><input type="text" name="gene_id" value ="'. $gene_id .' disabled"> <br>';
      echo '<b>Gene biotype : </b><input type="text" required name="gene_biotype" disabled><br>';
      echo '<b>Transcript biotype : </b><input type="text" required name="transcript_biotype" disabled><br>';
      echo '<b> Gene symbol : </b><input type ="text" required name = "gene_symbol" disabled><br>';
      echo '<b> Description : </b><input type ="text" required name = "gene_description" disabled><br>';
      echo '</form>';
      echo '</td>';
    }
    ?>

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

    <tr>
      <td align='center'> <input type="submit" value="Send" name="send_annotation">
      <input type="submit" value="Save" name="save_annotation"> </td>
    </tr>

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
      echo "
        This is your first attempt
    ";
    }

    echo '</tbody>';
    echo '</table>';
    ?>

  </div>

</body>

</html>
