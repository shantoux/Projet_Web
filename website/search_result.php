<?php session_start();?>

<html>
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
          echo "<a href=\"./forum.php\">Forum</a>";
        }
        if ($_SESSION['role'] == 'Administrator'){
          echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
          echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
          echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
          echo "<a href=\"./forum.php\">Forum</a>";
          echo "<a href=\"./user_list.php\">Users' List</a>";
        }
      ?>
      <a href="about.php">About</a>
      <a class="disc" href="disconnect.php">Disconnect</a>
      <a class="disc"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>lais
    </div>
  <br>

  <div id="pagetitle">
    Search results
  </div>

  <?php
    include_once 'libphp/dbutils.php';
    connect_db();
  ?>
  <br>

  <!-- Display table of results for the search -->
  <div id="element1">
    <?php
      ### DISPLAY RESULTS AS GENOMES
      if ($_POST["search_type"] == "genome") {
      echo '<table class = "table_type1">';

      # display first line
      echo '<thead>';
      echo '<tr>';
      echo '<th>Specie / Name / Strain</th>';
      echo '<th>Size of stored genome</th>';
      echo '</tr>';
      echo '</thead>'; #end of first line

      # display results of search
      echo '<tbody>';
      $query = "SELECT DISTINCT GENO.genome_id, GENO.genome_seq FROM database_projet.genome AS GENO";
      $conditions = false;
      # test if one condition has been filled in the search form
      foreach (array("specie", "nucl_sequence") as $token) {
        if ($_POST[$token] != "") {
          $conditions = true;
          break;
        }
      }
      foreach (array("seq_id", "pep_sequence") as $token) {
        if ($_POST[$token] != "") {
          $conditions = true;
          $query = $query . ", database_projet.gene AS GENE";
        }
      }
      foreach (array("genes", "description") as $token) {
        if ($_POST[$token] != "") {
          $conditions = true;
          $query = $query . ", database_projet.annotations AS A";
          break;
        }
      }
      # add conditions to request
      if ($conditions) {
        $first_cond = true;
        $query = $query . " WHERE ";
        # joint the tables
        if ($_POST["pep_sequence"] != "" || $_POST["seq_id"] != "") {
          $query = $query . "GENO.genome_id = GENE.genome_id";
          $first_cond = false;
        }
        if ($_POST["genes"] != "" || $_POST["description"] != "") {
          if (!$first_cond) {$query = $query . " AND ";}
          else {$first_cond = false;}
          $query = $query . "GENO.genome_id = A.genome_id";
        }
        # check for condition on specie name
        if ($_POST["specie"] != "") {
          if (!$first_cond) {$query = $query . " AND ";}
          else {$first_cond = false;}
          $query = $query . "GENO.genome_id LIKE '%" . $_POST["specie"] . "%'";
        }
        # check for condition on sequence id
        if ($_POST["seq_id"] != "") {
          if (!$first_cond) {$query = $query . " AND ";}
          else {$first_cond = false;}
          $query = $query . "GENE.sequence_id LIKE '%" . $_POST["seq_id"] . "%'";
        }
        # check for condition on nucleotides sequence
        if ($_POST["nucl_sequence"] != "") {
          if (!$first_cond) {$query = $query . " AND ";}
          else {$first_cond = false;}
          $query = $query . "GENO.genome_seq LIKE '%" . $_POST["nucl_sequence"] . "%'";
        }
        # check for condition on peptides sequence
        if ($_POST["pep_sequence"] != "") {
          if (!$first_cond) {$query = $query . " AND ";}
          else {$first_cond = false;}
          $query = $query . "GENE.prot_seq LIKE '%" . $_POST["pep_sequence"] . "%'";
        }
        # check for condition on genes names
        if ($_POST["genes"] != "") {
          if (!$first_cond) {$query = $query . " AND ";}
          else {$first_cond = false;}
          $query = $query . "A.gene_id LIKE '%" . $_POST["genes"] . "%'";
        }
        # check for condition on genes descriptions
        if ($_POST["description"] != "") {
          if (!$first_cond) {$query = $query . " AND ";}
          else {$first_cond = false;}
          $query = $query . "A.description LIKE '%" . $_POST["description"] . "%'";
        }
      }
      # query is complete!
      $query = $query . ";";
      # run query
      $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
      if(pg_num_rows($result) > 0){
        for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++) {
          $g_id = pg_fetch_result($result, $res_nb, 0); //récupère le résultat de la 1e colonne (0), $res_nb ieme ligne ($res_nb)
          $g_size = strlen(pg_fetch_result($result, $res_nb, 1));
          echo '<tr><td>';
          echo "<a href=\"./genome_info.php?id=" . $g_id . "\">$g_id</a></td><td>$g_size";
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
      }

      ### DISPLAY RESULTS AS GENE/PROT
      if ($_POST["search_type"] == "gene_prot") {
        echo '<form action="download_fasta.php" method="post" target="_blank">';
        echo '<table class = "table_type1">';

        # display first line
        echo '<thead>';
        echo '<tr>';
        echo '<th>Sequence identifier</th>';
        echo '<th>Specie</th>';
        echo '<th>Size of sequence</th>';
        echo '<th>Annotated</th>';
        echo '<th>Extract</th>';
        echo '</tr>';
        echo '</thead>'; #end of first line

        # display results of search
        echo '<tbody>';
        $query = "SELECT DISTINCT GENE.sequence_id, GENE.genome_id, GENE.gene_seq FROM database_projet.gene AS GENE";
        $conditions = false;
        # test if one condition has been filled in the search form
        foreach (array("specie", "nucl_sequence", "seq_id", "pep_sequence") as $token) {
          if ($_POST[$token] != "") {
            $conditions = true;
            break;
          }
        }
        foreach (array("genes", "description") as $token) {
          if ($_POST[$token] != "") {
            $conditions = true;
            $query = $query . ", database_projet.annotations AS A";
            break;
          }
        }
        # add conditions to request
        if ($conditions) {
          $first_cond = true;
          $query = $query . " WHERE ";
          # joint the tables
          if ($_POST["genes"] != "" || $_POST["description"] != "") {
            $first_cond = false;
            $query = $query . "GENE.genome_id = A.genome_id AND GENE.sequence_id = A.sequence_id";
          }
          # check for condition on specie name
          if ($_POST["specie"] != "") {
            if (!$first_cond) {$query = $query . " AND ";}
            else {$first_cond = false;}
            $query = $query . "GENE.genome_id LIKE '%" . $_POST["specie"] . "%'";
          }
          # check for condition on sequence id
          if ($_POST["seq_id"] != "") {
            if (!$first_cond) {$query = $query . " AND ";}
            else {$first_cond = false;}
            $query = $query . "GENE.sequence_id LIKE '%" . $_POST["seq_id"] . "%'";
          }
          # check for condition on nucleotides sequence
          if ($_POST["nucl_sequence"] != "") {
            if (!$first_cond) {$query = $query . " AND ";}
            else {$first_cond = false;}
            $query = $query . "GENE.gene_seq LIKE '%" . $_POST["nucl_sequence"] . "%'";
          }
          # check for condition on peptides sequence
          if ($_POST["pep_sequence"] != "") {
            if (!$first_cond) {$query = $query . " AND ";}
            else {$first_cond = false;}
            $query = $query . "GENE.prot_seq LIKE '%" . $_POST["pep_sequence"] . "%'";
          }
          # check for condition on genes names
          if ($_POST["genes"] != "") {
            if (!$first_cond) {$query = $query . " AND ";}
            else {$first_cond = false;}
            $query = $query . "A.gene_id LIKE '%" . $_POST["genes"] . "%'";
          }
          # check for condition on genes descriptions
          if ($_POST["description"] != "") {
            if (!$first_cond) {$query = $query . " AND ";}
            else {$first_cond = false;}
            $query = $query . "A.description LIKE '%" . $_POST["description"] . "%'";
          }
        }
        # query is complete!
        $query = $query . ";";
        # run query
        $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
        if(pg_num_rows($result) > 0){
          for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++) {
            $s_id = pg_fetch_result($result, $res_nb, 0);
            $g_id = pg_fetch_result($result, $res_nb, 1);
            $s_size = strlen(pg_fetch_result($result, $res_nb, 2));
            // display sequence id
            echo '<tr><td>';
            echo "<a href=\"./sequence_info.php?id=" . $s_id . "\">$s_id</a></td>";
            // display genome / specie / strain
            echo "<td><a href=\"./genome_info.php?id=" . $g_id . "\">$g_id</a></td>";
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
      }
      echo '<br><input type="submit" name="extracted" value="Extract selected sequences">';
    ?>
  </div>
</body>
</html>
