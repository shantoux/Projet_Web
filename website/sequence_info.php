<!-- Web page to get information about gene or protein -->

<?php session_start();

  // check if user is logged in: else, redirect to login page
  if (!isset($_SESSION['user'])) {
    echo '<script>location.href="login.php"</script>';
  }

?>

<!DOCTYPE html>
<html>

  <!-- Page header -->
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gene/Protein information</title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">

    <?php
      // retrieve the sequence identifier through GET method
      $seq_id = $_GET['sid'];

      // if specified, open alternative database
      if (isset($_POST["websites"])) {

        // load uniprot
        if ($_POST["websites"] == "Uniprot") {
          echo '<script>location.href="https://www.uniprot.org/uniprot/?query=' . $seq_id . '&sort=score"</script>';
        }

        // load EMBL-EBI
        elseif ($_POST["websites"] == "Embl") {
          echo '<script>location.href="https://www.ebi.ac.uk/ebisearch/search.ebi?db=allebi&query=' . $seq_id . '&requestFrom=searchBox"</script>';
        }
      }

    ?>

    <!-- display menu options depending of the user's role -->
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

    <!-- Display page title -->
    <h2 id="pagetitle">
      Gene/Protein information
    </h2>

    <?php
      // import db functions
      include_once 'libphp/dbutils.php';
      connect_db();

      // retrieve gene informations
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

      <!-- Display page title -->
      <table class="table_type_seq_inf">
        <colgroup>
          <col span="1" style="width: 80%;">
          <col span="1" style="width: 10%;">
        </colgroup>
        <tr colspan=2>
          <td>

            <?php
              // display basic gene information
              echo "<b>Sequence identifier:</b> $seq_id<br><br>";
              echo "<b>Specie:</b> $genome_id<br>";
              echo "<b>Chromosome:</b> $chromosome<br>";
              echo "Sequence is " . strlen($gene_seq) . " nucleotides long - it starts on position <b>" . $start_seq . "</b> and ends on position <b>" . $end_seq . "</b>.<br><br>";

              // look for annotations
              $query_annot = "SELECT genome_id, gene_id, sequence_id, gene_biotype, transcript_biotype, gene_symbol, description, annotator, status
              FROM database_projet.annotations
              WHERE genome_id = '" . $genome_id . "' AND sequence_id = '" . $seq_id . "' AND status != 'rejected' ORDER BY attempt DESC;";
              $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());

              // check if there is an annotation
              if(pg_num_rows($result_annot) > 0){
                // retrieve name of annotator
                $annotator="SELECT U.first_name, U.last_name
                FROM database_projet.users U
                WHERE U.email='" . pg_fetch_result($result_annot, 0, 7) . "';";
                $result2 = pg_query($db_conn, $annotator) or die('Query failed with exception: ' . pg_last_error());
                $annotator_first_name= pg_fetch_result($result2, 0, 0);
                $annotator_last_name= pg_fetch_result($result2, 0, 1);

                // display annotator name
                echo "This sequence has been annotated by " . $annotator_first_name . " " . $annotator_last_name . ".<br>";

                $waiting = false;
                // display warning if annotation is not validated yet
                if (pg_fetch_result($result_annot, 0, 8) == 'waiting') {
                  $waiting = true;
                  echo '<span style="color:#A3423C;">' . "<br><b>WARNING</b>: THIS ANNOTATION HAS NOT BEEN VALIDATED YET! Use with caution.<br><br>";
                }

                // display gene biotype
                if (pg_fetch_result($result_annot, 0, 3) != "") {
                  echo "<b>Gene biotype:</b> " . pg_fetch_result($result_annot, 0, 3) . "<br>";
                }

                // display transcript biotype
                if (pg_fetch_result($result_annot, 0, 4) != "") {
                  echo "<b>Transcript biotype:</b> " . pg_fetch_result($result_annot, 0, 4) . "<br>";
                }

                // display gene symbol
                if (pg_fetch_result($result_annot, 0, 5) != "") {
                  echo "<b>Gene symbol:</b> " . pg_fetch_result($result_annot, 0, 5) . "<br>";
                }

                // display description
                if (pg_fetch_result($result_annot, 0, 6) != "") {
                  echo "<b>Description:</b> " . pg_fetch_result($result_annot, 0, 6) . "<br>";
                }

                if ($waiting) {
                  echo '</span>';
                }
              }

              // warn if gene is not annotated
              else {
                echo "This gene is not annotated.<br>";
              }
            ?>
        </td>
        </tr>
        <tr>
        </tr>

        <!-- Display nucleotidic sequence -->
        <tr>
          <td>
            Gene sequence<br>
            <div style="font-family:courier;border:solid 1px black;background-color:white;"><?php echo $gene_seq;?></div>
          </td>

          <!-- display button for automative blast alignment of the nucleotidic sequence -->
          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $gene_seq . "&type=nucl\" target=\"_blank\">"?>
                 <button class="button_neutral" type="button">Align with Blast</button>
                 </a>
          </td>
        </tr>

        <!-- Display peptidic sequence -->

        <?php
        // we look for the known domains of the protein
        $domains = array();

        // retrieve the simple html dom functions (thank you very much to the original git creator!!!)
        include_once 'libphp/simplehtmldom/simple_html_dom.php';

        // build html element corresponding to the adress of the uniprot page of the protein
        $adress = 'https://www.uniprot.org/uniprot/?query=' . $seq_id . '&sort=score';
        $html = file_get_html($adress);

        // retrieve the Uniprot identifier of the protein with simple dom functions
        $uniprot_protein_name = $html->find(".entryID", 0)->plaintext;

        // use it to build the PFAM adress for the protein
        $adress = 'https://pfam.xfam.org/protein/' . $uniprot_protein_name;

        // retrieve the <tbody> element in which the domains are stored on the PFAM page
        $t = file_get_html($adress);
        $t = $t->find("table#imageKey.resultTable.details", 0);

        // cehck if we find any domain
        $no_children = true;
        foreach($html->find('div.article') as $article) {
          $no_children = false;
          break;
        }

        if (!$no_children) {
          $t = $t->children(1);

          // loop on all of its lines
          for ($domain_index=0; $domain_index<sizeof($t->children); $domain_index++) {

            // retrieve the domains informations
            $domain = array();
            $domain["name"] = $t->children($domain_index)->children(1)->plaintext;
            $domain["start_pos"] = $t->children($domain_index)->children(2)->plaintext;
            $domain["end_pos"] = $t->children($domain_index)->children(3)->plaintext;
            $domains[$domain_index] = $domain;
          }
        }
        ?>

        <tr>
          <td>
            Peptide sequence<br>
            <div style="font-family:courier;border:solid 1px black;background-color:white;">
            <?php
              // build list of background colors
              $colors = array("#ffe119", "#3cb44b", "#f58231", "#42d4f4", "#f032e6");

              $last_domain_end = 0;

              // loop on all domains
              for ($domain_ind=0; $domain_ind<sizeof($domains); $domain_ind++) {

                // check if domain is known
                if ($domains[$domain_ind]["name"] != "n/a") {

                  // display protein region since last domain
                  echo substr($prot_seq, $last_domain_end, $domains[$domain_ind]["start_pos"] - $last_domain_end);

                  // display background colors based on domains
                  $color = $colors[$domain_ind % sizeof($colors)];
                  echo '<span style="background-color:' . $color . ';">';
                  echo substr($prot_seq, $domains[$domain_ind]["start_pos"], $domains[$domain_ind]["end_pos"] - $domains[$domain_ind]["start_pos"]);
                  echo '</span>';
                  $last_domain_end = $domains[$domain_ind]["end_pos"];
                }
              }
              echo substr($prot_seq, $last_domain_end);
            ?>
          </div>
          </td>

          <!-- display button for automative blast alignment of the peptidic sequence -->
          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $prot_seq . "&type=prot\" target=\"_blank\">"?>
                 <button class="button_neutral" type="button">Align with Blast</button>
                 </a>
        </tr>
        <tr colspan=2>
          <td>
            <?php

              // display protein domain names
              echo '<b> Found protein domains are:</b><br>';

              $no_know_domain = true;

              // loop on domain
              for ($domain_ind=0; $domain_ind<sizeof($domains); $domain_ind++) {

                // check if domain is known
                if ($domains[$domain_ind]["name"] != "n/a") {
                  $no_know_domain = false;
                  $color = $colors[$domain_ind % sizeof($colors)];
                  echo '<a href="https://pfam.xfam.org/family/' . $domains[$domain_ind]["name"] . '" style="background-color:' . $color . ';" target="_blank">';
                  echo $domains[$domain_ind]["name"];
                  echo '</a><br>';
                }
              }

              if ($no_know_domain) {
                echo "No domain found for this protein.";
              }
            ?>
          </td>
        </tr>

      </table>

      <!-- Display button to search in other bases with the sequence identifier -->
      Search other websites :
      <?php echo '<form action="sequence_info.php?sid=' . $seq_id . '" method="post" target="blank">';?>
      <select name="websites">
        <option value="Uniprot"> Uniprot </option>
        <option value="Embl"> Embl </option>
      </select>
      <input class="button_ok" type="submit" name="search" value="Search">
    </form>
    </div>


  </body>
</html>
