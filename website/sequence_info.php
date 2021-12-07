<!-- Web page to get information about gene or protein -->
<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gene/Protein information </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body>
    <?php
      # TODO: un-hardcode the user role, check in database for the actual role
      $role = "administrator";
      $roles = array("annotator", "validator", "administrator");
    ?>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search.php">New search</a>
        <?php
          if ($_SESSION['status'] == 'annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
          }
          if ($_SESSION['status'] == 'validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
          }
          if ($_SESSION['status'] == 'administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
    </div>

    <div id="pagetitle">
      Gene/Protein information
    </div>

    <?php
      include_once 'libphp/dbutils.php';
      connect_db();
      $seq_id = $_GET['id'];
      $query = "SELECT sequence_id, genome_id, start_seq, end_seq, chromosome, prot_seq, gene_seq FROM database_projet.gene WHERE sequence_id = '" . $seq_id . "';";
      $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
      $genome_id = pg_fetch_result($result, 0, 1);
      $start_seq = pg_fetch_result($result, 0, 2);
      $end_seq = pg_fetch_result($result, 0, 3);
      $chromosome = pg_fetch_result($result, 0, 4);
      $prot_seq = pg_fetch_result($result, 0, 5);
      $gene_seq = pg_fetch_result($result, 0, 6);
    ?>

    <div class="center">
      <table class="table_type3">
        <tr colspan=2>
          <td>
            <?php
              echo "<b>Sequence identifier:</b> $seq_id<br><br>";
              echo "<b>Specie:</b> $genome_id<br>";
              echo "<b>Chromosome:</b> $chromosome<br>";
              echo "Sequence is " . strlen($gene_seq) . " nucleotides long - it starts on position <b>" . $start_seq . "</b> and ends on position <b>" . $end_seq . "</b>.<br><br>";
              ## check for annotations
              $query_annot = "SELECT genome_id, gene_id, sequence_id, gene_biotype, transcript_biotype, gene_symbol, description, annotator FROM database_projet.annotations WHERE genome_id = '" . $genome_id . "' AND sequence_id = '" . $seq_id . "';";
              $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());
              if(pg_num_rows($result_annot) > 0){
                $annotator="SELECT U.first_name, U.last_name
                FROM database_projet.users U
                WHERE U.email='" . pg_fetch_result($result_annot, 0, 7) . "';";
                $result2 = pg_query($db_conn, $annotator) or die('Query failed with exception: ' . pg_last_error());
                $annotator_first_name= pg_fetch_result($result2, 0, 0);
                $annotator_last_name= pg_fetch_result($result2, 0, 1);
                echo "This sequence has been annotated by " . $annotator_first_name . " " . $annotator_last_name . ".<br>";
                if (pg_fetch_result($result_annot, 0, 3) != "") {
                  echo "<b>Gene biotype:</b> " . pg_fetch_result($result_annot, 0, 3) . "<br>";
                }
                if (pg_fetch_result($result_annot, 0, 4) != "") {
                  echo "<b>Transcript biotype:</b> " . pg_fetch_result($result_annot, 0, 4) . "<br>";
                }
                if (pg_fetch_result($result_annot, 0, 5) != "") {
                  echo "<b>Gene symbol:</b> " . pg_fetch_result($result_annot, 0, 5) . "<br>";
                }
                if (pg_fetch_result($result_annot, 0, 6) != "") {
                  echo "<b>Description:</b> " . pg_fetch_result($result_annot, 0, 6) . "<br>";
                }
              }
              else {
                echo "This gene is not annotated.<br>";
              }
            ?>
        </td>
        </tr>
        <tr>
        </tr>

        <tr>
          <td>
            Gene sequence<br>
            <textarea id="seq" name="seq"
            rows="8" cols="80" readonly><?php echo $gene_seq;?></textarea>
          </td>
          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $gene_seq . "&type=nucl\" target=\"_blank\">"?>
                 <button type="button">Align with Blast</button>
                 </a>
          </td>
        </tr>

        <tr>
          <td>
            Peptide sequence<br>
            <textarea id="seq" name="seq"
            rows="8" cols="80" readonly><?php echo $prot_seq;?> </textarea>
          </td>
          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $prot_seq . "&type=prot\" target=\"_blank\">"?>
                 <button type="button">Align with Blast</button>
                 </a>
        </tr>

      </table>

      Search other websites :
      <select name="websites">
        <option value="Uniprot"> Uniprot </option>
        <option value="Embl"> Embl </option>
      </select>
      <a href="https://www.uniprot.org">
           <button type="button">Search</button>
           </a>
    </div>
  </body>
</html>
