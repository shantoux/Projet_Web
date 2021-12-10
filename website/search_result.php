<!-- Web page for the results of a search -->

<?php session_start();

  // check if user is logged in: else, redirect to login page
  if (!isset($_SESSION['user'])) {
    echo '<script>location.href="login.php"</script>';
  }

?>

<!DOCTYPE html>
<html>

  <!-- Page header -->
  <head> <meta charset="UTF-8">
    <title>Database search result</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
  </head>

  <body class="center">

    <!-- display top navigation bar -->
    <div class="topnav">
      <a href="./search.php">New search</a>
      <?php
          if ($_SESSION['role'] == 'Annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
        <a class="role"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
      </div>
    <br>

    <!-- Display page title -->
    <h2 id="pagetitle">
      Search results
    </h2>

    <?php
    // import db functions
      include_once 'libphp/dbutils.php';
      connect_db();
    ?>

    <br>

    <!-- Display table of results for the search -->
    <div id="element1">

      <?php

        ### DISPLAY RESULTS AS GENOMES
        // check if the search was on genomes
        if ($_POST["search_type"] == "genome") {

          // initiate table
          echo '<table class = "table_type1">';

          // display first line
          echo '<thead>';
          echo '<tr>';
          echo '<th>Specie / Name / Strain</th>';
          echo '<th>Size of stored genome (nb of nucl)</th>';
          echo '</tr>';
          echo '</thead>';

          // display results of the search
          echo '<tbody>';

          // create the query to get the result from the DB
          // the smallest form includes the desired attributes i.e. the genome id and whole sequence (to get the size)
          $query = "SELECT DISTINCT GENO.genome_id, GENO.genome_seq FROM database_projet.genome AS GENO";

          // create boolean to see if any conditions were filled in the search form
          $conditions = false;

          // test if one condition has been filled in the search form
          foreach (array("specie", "nucl_sequence") as $token) {
            if ($_POST[$token] != "") {
              $conditions = true;
              break;
            }
          }

          // if condition on the sequence id or the peptidic sequence, add the 'gene' table to the query
          foreach (array("seq_id", "pep_sequence") as $token) {
            if ($_POST[$token] != "") {
              $conditions = true;
              $query = $query . ", database_projet.gene AS GENE";
            }
          }

          // if condition on the genes id or description, add the 'annotations' table to the query
          foreach (array("gene_symb", "gene_id", "gene_biotype", "transcript_biotype", "description") as $token) {
            if ($_POST[$token] != "") {
              $conditions = true;
              $query = $query . ", database_projet.annotations AS A";
              break;
            }
          }

          // add conditions to request
          if ($conditions) {
            $first_cond = true;
            $query = $query . " WHERE ";

            // joint the tables
            // if condition on the sequence id or the peptidic sequence, join tables 'genome' and 'gene'
            if ($_POST["pep_sequence"] != "" || $_POST["seq_id"] != "") {
              $query = $query . "GENO.genome_id = GENE.genome_id";
              $first_cond = false;
            }

            // if condition on the genes id or description, join tables 'genome' and 'annotations' and control annotation status
            if ($_POST["gene_symb"] != "" || $_POST["gene_id"] != "" || $_POST["gene_biotype"] != "" || $_POST["transcript_biotype"] != "" || $_POST["description"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENO.genome_id = A.genome_id AND A.status != 'rejected'";
            }

            // check for condition on specie name and add it to the query
            if ($_POST["specie"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENO.genome_id LIKE '%" . $_POST["specie"] . "%'";
            }

            // check for condition on sequence id and add it to the query
            if ($_POST["seq_id"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENE.sequence_id LIKE '%" . $_POST["seq_id"] . "%'";
            }

            // check for condition on nucleotides sequence and add it to the query
            if ($_POST["nucl_sequence"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENO.genome_seq LIKE '%" . $_POST["nucl_sequence"] . "%'";
            }

            // check for condition on peptides sequence and add it to the query
            if ($_POST["pep_sequence"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENE.prot_seq LIKE '%" . $_POST["pep_sequence"] . "%'";
            }

            // check for condition on genes id and add it to the query
            if ($_POST["gene_id"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.gene_id LIKE '%" . $_POST["gene_id"] . "%'";
            }

            // check for condition on gene symbol id and add it to the query
            if ($_POST["gene_symb"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.gene_symbol LIKE '%" . $_POST["gene_symb"] . "%'";
            }

            // check for condition on genes biotype and add it to the query
            if ($_POST["gene_biotype"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.gene_biotype LIKE '%" . $_POST["gene_biotype"] . "%'";
            }

            // check for condition on transcript biotype and add it to the query
            if ($_POST["transcript_biotype"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.transcript_biotype LIKE '%" . $_POST["transcript_biotype"] . "%'";
            }

            // check for condition on genes descriptions and add it to the query
            if ($_POST["description"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.description LIKE '%" . $_POST["description"] . "%'";
            }
          }
          ### query finally is complete! Add last ';'
          $query = $query . ";";

          // send the query to the DB
          $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

          // check if any result was found for this query
          if(pg_num_rows($result) > 0){

            // loop on all results and display genome id and size of its sequence
            for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++) {
              $g_id = pg_fetch_result($result, $res_nb, 0); //récupère le résultat de la 1e colonne (0), $res_nb ieme ligne ($res_nb)
              $g_size = strlen(pg_fetch_result($result, $res_nb, 1));
              echo '<tr><td>';
              echo "<a href=\"./genome_info.php?gid=" . $g_id . "\">$g_id</a></td><td>$g_size";
              echo '</td></tr>';
            }
          }

          // display bad alert event if no result found
          else{
            echo "<div class=\"alert_bad\">
            <span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>
            No result found in base.</div>";
          }

          echo '</tbody>';
          echo '</table>';
        }

        ### DISPLAY RESULTS AS GENE/PROT
        // check if the search was on sequences
        if ($_POST["search_type"] == "gene_prot") {

          // create form for the extraction of sequences
          echo '<form action="download_fasta.php" method="post" target="_blank">';

          // initiate table
          echo '<table class = "table_type1">';

          // display first line
          echo '<thead>';
          echo '<tr>';
          echo '<th>Sequence identifier</th>';
          echo '<th>Specie</th>';
          echo '<th>Size of sequence (nb of nucl)</th>';
          echo '<th>Annotated</th>';
          echo '<th>Extract</th>';
          echo '</tr>';
          echo '</thead>';

          // display results of search
          echo '<tbody>';

          // create the query to get the result from the DB
          // the smallest form includes the desired attributes i.e. the sequence, its id and corresponding genome
          $query = "SELECT DISTINCT GENE.sequence_id, GENE.genome_id, GENE.gene_seq FROM database_projet.gene AS GENE";

          // create boolean to see if any conditions were filled in the search form
          $conditions = false;

          // test if one condition has been filled in the search form
          foreach (array("specie", "nucl_sequence", "seq_id", "pep_sequence") as $token) {
            if ($_POST[$token] != "") {
              $conditions = true;
              break;
            }
          }

          // if condition on the genes id or description, add the 'annotations' table to the query
          foreach (array("gene_symb", "gene_id", "gene_biotype", "transcript_biotype", "description") as $token) {
            if ($_POST[$token] != "") {
              $conditions = true;
              $query = $query . ", database_projet.annotations AS A";
              break;
            }
          }

          // add conditions to request
          if ($conditions) {
            $first_cond = true;
            $query = $query . " WHERE ";

            // joint the tables
            // if condition on the genes id or description, join tables 'gene' and 'annotations' and control annotation status
            if ($_POST["gene_symb"] != "" || $_POST["gene_id"] != "" || $_POST["gene_biotype"] != "" || $_POST["transcript_biotype"] != "" || $_POST["description"] != "") {
              $first_cond = false;
              $query = $query . "GENE.sequence_id = A.sequence_id AND A.status != 'rejected'";
            }

            // check for condition on specie name and add it to the query
            if ($_POST["specie"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENE.genome_id LIKE '%" . $_POST["specie"] . "%'";
            }

            // check for condition on sequence id and add it to the query
            if ($_POST["seq_id"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENE.sequence_id LIKE '%" . $_POST["seq_id"] . "%'";
            }

            // check for condition on nucleotides sequence and add it to the query
            if ($_POST["nucl_sequence"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENE.gene_seq LIKE '%" . $_POST["nucl_sequence"] . "%'";
            }

            // check for condition on peptides sequence and add it to the query
            if ($_POST["pep_sequence"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "GENE.prot_seq LIKE '%" . $_POST["pep_sequence"] . "%'";
            }

            // check for condition on genes id and add it to the query
            if ($_POST["gene_id"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.gene_id LIKE '%" . $_POST["gene_id"] . "%'";
            }

            // check for condition on gene symbol id and add it to the query
            if ($_POST["gene_symb"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.gene_symbol LIKE '%" . $_POST["gene_symb"] . "%'";
            }

            // check for condition on genes biotype and add it to the query
            if ($_POST["gene_biotype"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.gene_biotype LIKE '%" . $_POST["gene_biotype"] . "%'";
            }

            // check for condition on transcript biotype and add it to the query
            if ($_POST["transcript_biotype"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.transcript_biotype LIKE '%" . $_POST["transcript_biotype"] . "%'";
            }

            // check for condition on genes descriptions and add it to the query
            if ($_POST["description"] != "") {
              if (!$first_cond) {$query = $query . " AND ";}
              else {$first_cond = false;}
              $query = $query . "A.description LIKE '%" . $_POST["description"] . "%'";
            }
          }
          ### query finally is complete! Add last ';'
          $query = $query . ";";

          // send the query to the DB
          $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

          // check if any result was found for this query
          if(pg_num_rows($result) > 0){

            // loop on all results and display informations
            for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++) {
              $s_id = pg_fetch_result($result, $res_nb, 0);
              $g_id = pg_fetch_result($result, $res_nb, 1);
              $s_size = strlen(pg_fetch_result($result, $res_nb, 2));

              // display sequence id with link to sequence page
              echo '<tr><td>';
              echo "<a href=\"./sequence_info.php?sid=" . $s_id . "\">$s_id</a></td>";

              // display genome / specie / strain with link to genome page
              echo "<td><a href=\"./genome_info.php?gid=" . $g_id . "\">$g_id</a></td>";

              // display size of nucleotidic sequence
              echo "<td>$s_size</td><td>";

              // display a character indicating wether the sequence is annotated, not annotated or with an annotation waiting for validation
              $query_annot = "SELECT status FROM database_projet.annotations WHERE genome_id = '" . $g_id . "' AND sequence_id = '" . $s_id . "' AND status != 'rejected';";
              $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());
              if(pg_num_rows($result_annot) > 0){
                $status_annot = pg_fetch_result($result_annot,0,0);
                if ($status_annot == 'validated'){
                echo "<span style=\"color:green;\">&#10004</span>";
                }
                else{
                echo "<span style=\"color:red;\">&#8987</span>";
                }
              }
              else {
                echo "<span style=\"color:red;\">&#10008</span>";
              }
              echo '</td><td>';
              echo '<input type="checkbox" name="extracted_seq[]" value="' . $s_id . '">';
              echo '</td></tr>';
            }
          }
          else{
            echo "<div class=\"alert_bad\">
            <span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>
            No result found in base.</div>";
          }

          echo '</tbody>';
          echo '</table>';

          echo '<br><input class="button_neutral" type="submit" name="extracted" value="Extract selected sequences">';
        }

      ?>
    </div>
  </body>
</html>
