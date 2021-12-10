<!-- Web page to get information about genome -->

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
    <title>Genome information </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">

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


    <h2 id="pagetitle">
      Genome information
    </h2>

    <!-- store genome -->
    <?php
      # initialize a variable for the number of characters to display per line
      $char_per_line = 70;
      if (isset($_POST["nb_nucl_per_line"])) {
        $char_per_line = $_POST["nb_nucl_per_line"];
      }
      # retrieve genome informations
      $genome_id = $_GET['gid'];
      echo "<br>";
    ?>

    <div class="center">
      <table class="table_type_gene_inf">
        <colgroup>
          <col span="1" style="width: 7%;">
          <col span="1" style="width: 70%;">
          <col span="1" style="width: 7%;">
          <col span="1" style="width: 16%;">
        </colgroup>

        <!-- display header line -->
        <thead>
          <tr>
            <th colspan=2 class="type2"  align='left'>Genome's name : Ecoli</th>
            <th colspan=2 text-align='right' horizontal-align="middle">
              <?php
                $url_suffix = "?gid=" . $genome_id;
                echo '<form action="genome_info.php' . $url_suffix . '" method="post">';
              ?>
                Nb of nucl. per line:
                <input type="text" name="nb_nucl_per_line" maxlength="4" size="4" value="<?php echo $char_per_line; ?>">
                <input class="button_blue" type="submit" value="Update">
              </form>
            </th>
          </tr>
        </thead>

        <tbody>
          <tr>

          <?php

            // import db functions
            include_once 'libphp/dbutils.php';
            connect_db();

            // retrieve genome sequence from the DB
            $query = "SELECT genome_seq FROM database_projet.genome WHERE genome_id = '" . $genome_id . "';";
            $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
            $genome_whole_seq = pg_fetch_result($result, 0, 0);
            $genome_size = strlen($genome_whole_seq);

            // display the position in the genome of the first character of the line in the left-most cell
            echo "<td align='right'>";
            $nb_of_lines = intdiv($genome_size, $char_per_line) + 1;
            for ($line = 0; $line < $nb_of_lines; $line++) {
              $char = $line*$char_per_line + 1;
              echo "$char-<br>";
            }
            echo '</td>';

            // display genome sequence in the second cell
            echo "<td align='left'>";

            // extend query max time because it takes quite some time to retrieve and display whole genome
            ini_set('max_execution_time', '300'); //300 seconds = 5 minutes

            // retrieve all genes of this genome from the DB
            $query = "SELECT sequence_id, start_seq, end_seq, gene_seq FROM database_projet.gene WHERE genome_id = '" . $genome_id . "' ORDER BY start_seq;";
            $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

            // stores the position we are at in the genome
            $nucl_ind_count = 0;

            // stores the number of characters left on the current line
            $count = $char_per_line;

            // retrieve gene informations
            for ($gene_ind = 0; $gene_ind < pg_num_rows($result); $gene_ind++) {

              // store gene informations
              $seq_id = pg_fetch_result($result, $gene_ind, 0);
              $seq_start = pg_fetch_result($result, $gene_ind, 1);
              $seq_end = pg_fetch_result($result, $gene_ind, 2);
              $gene_seq = pg_fetch_result($result, $gene_ind, 3);

              // control if that sequence does not overlap with previously displayed sequences: if it does, skip sequence
              if ($seq_start > $nucl_ind_count) {

                // display intergenic part immediately before gene, with a given number of characters per line
                echo '<span style="font-family:Consolas;">'; # set style
                $seq_to_display = substr($genome_whole_seq, $nucl_ind_count, $seq_start-$nucl_ind_count-1);
                while (strlen($seq_to_display) > $count) {
                  echo substr($seq_to_display, 0, $count);
                  echo '<br>';
                  $seq_to_display = substr($seq_to_display, $count);
                  $count = $char_per_line;
                }
                echo $seq_to_display;

                // actualize number of characters left on the current line
                $count = $count - strlen($seq_to_display);

                // add link of the sequence page (make the gene clickable)
                echo '</span>';
                echo "<a href=\"./sequence_info.php?sid=" . $seq_id . "\" ";

                // check if gene is annotated
                $query_annot = "SELECT gene_id, gene_symbol, description, annotator, status
                FROM database_projet.annotations
                WHERE sequence_id = '" . $seq_id . "' AND genome_id = '" . $genome_id . "' AND status != 'rejected' AND status != 'assigned';";
                $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());

                // if it's not, display warning in mouse-over text and set color to red or orange -if the annotation is written but not validated yet
                if(pg_num_rows($result_annot) == 0) {
                  $color = "red";
                  $info = ' title="' . "WARNING: Unannotated gene";
                  echo 'style="font-family:Consolas;color:' . $color . ';"' . $info . '">';
                }

                // if it is annotated, display informations in mouse-over text and set color to blue (or orange -if the annotation is written but not validated yet)
                else {
                  $color = "blue";
                  if (pg_fetch_result($result_annot, 0, 4) == 'waiting') {
                    $color = '#A3423C';
                  }
                  $info = ' title="';
                  // add gene symbol if it exists
                  if (pg_fetch_result($result_annot, 0, 1) != "") {
                    $info = $info . pg_fetch_result($result_annot, 0, 1) . "\n";
                  }
                  $info = $info . pg_fetch_result($result_annot, 0, 0) . "\n" . pg_fetch_result($result_annot, 0, 2) . "\nClick to see annotation";
                  echo 'style="font-family:Consolas;color:' . $color . ';"' . $info . '">';
                }

                // display gene sequence, with a given number of characters per line
                $seq_to_display = $gene_seq;
                while (strlen($seq_to_display) > $count) {
                  echo substr($seq_to_display, 0, $count);
                  echo '<br>';
                  $seq_to_display = substr($seq_to_display, $count);
                  $count = $char_per_line;
                }
                echo $seq_to_display;

                // actualize number of characters left on the current line
                $count = $count - strlen($seq_to_display);
                echo '</a>';

                // actualize the position we are at in the genome
                $nucl_ind_count = $seq_end;
              }
            }

            // display end of genome, with a given number of characters per line
            echo '<span style="font-family:Consolas;">';
            $seq_to_display = substr($genome_whole_seq, $nucl_ind_count);
            while (strlen($seq_to_display) > $count) {
              echo substr($seq_to_display, 0, $count);
              echo '<br>';
              $seq_to_display = substr($seq_to_display, $count);
              $count = $char_per_line;
            }
            echo $seq_to_display;

            // actualize number of characters left on the current line
            $count = $count - strlen($seq_to_display);
            echo '</span>';
            echo '</td>';

            // display the position in the genome of the last character of the line in the third cell
            echo "<td align='left'>";
            $nb_of_lines = intdiv($genome_size, $char_per_line) + 1;
            for ($line = 1; $line < $nb_of_lines; $line++) {
              $char = $line*$char_per_line;
              echo "-$char <br>";
            }
            echo "-$genome_size";
            echo '</td>';
          ?>

          <!-- display the sequence id (and gene symbol when available) for each sequence in the right-most cell -->
          <td>
            <?php

              // store the line we are at
              $line_ind = 0;

              // retrieve gene location
              $query = "SELECT sequence_id, start_seq, end_seq, gene_seq FROM database_projet.gene WHERE genome_id = '" . $genome_id . "' ORDER BY start_seq;";
              $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

              // make sure two genes are not displayed on the same line
              $gene_on_line = false;

              // loop on all genes
              for ($gene_ind = 0; $gene_ind < pg_num_rows($result); $gene_ind++) {
                $seq_id = pg_fetch_result($result, $gene_ind, 0);
                $seq_start = pg_fetch_result($result, $gene_ind, 1);
                $gene_line = intdiv($seq_start, $char_per_line);

                // reach the line of the beginning of the gene sequence
                while ($line_ind < $gene_line) {
                  $line_ind = $line_ind + 1;
                  $gene_on_line = false;
                  echo "<br>";
                }

                // skip one more line if a gene is already displayed there
                if ($gene_on_line) {
                  echo "<br>";
                  $line_ind = $line_ind + 1;
                }
                $gene_on_line = true;

                // display gene with mouse-over text and hyperlink
                echo "<a href=\"./sequence_info.php?sid=" . $seq_id . "\" ";
                echo 'style="color:blue;" title="Clik to see sequence page.">&#8592;';
                echo $seq_id;

                // check if gene has gene_symbol
                $query_annot = "SELECT gene_symbol FROM database_projet.annotations WHERE sequence_id = '" . $seq_id . "' AND genome_id = '" . $genome_id . "';";
                $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());

                // if yes, display it between parenthesis
                if(pg_num_rows($result_annot) > 0) {
                  $gene_symb = pg_fetch_result($result_annot, 0, 0);
                  echo "($gene_symb)";
                }
                echo '</a> ';
              }

              // print empty lines until the end of genome whole sequence
              $nb_of_lines = intdiv($genome_size, $char_per_line) + 1;
              while ($line_ind < $nb_of_lines) {
                $line_ind = $line_ind + 1;
                echo "<br>";
              }
            ?>
          </td>
        </tbody>
      </table>
    </div>
  </body>
</html>
