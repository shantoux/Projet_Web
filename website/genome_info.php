<!-- Web page to get information about genome -->

<?php session_start();?>

<!DOCTYPE html>
<html>

  <!-- Page header -->
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Genome information </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
    </div>


    <div id="pagetitle">
      Genome information
    </div>

    <!-- store genome -->
    <?php
      # initialize a variable for the number of characters to display per line
      $char_per_line = 70;
      if (isset($_POST["nb_nucl_per_line"])) {
        $char_per_line = $_POST["nb_nucl_per_line"];
      }
      # retrieve genome informations
      $genome_id = $_GET['id'];
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
                $url_suffix = "?id=" . $genome_id;
                echo '<form action="genome_info.php' . $url_suffix . '" method="post">';
              ?>
                Nb of nucl. per line:
                <input type="text" name="nb_nucl_per_line" maxlength="4" size="4" value="<?php echo $char_per_line; ?>">
                <input type="submit" value="Update">
              </form>
            </th>
          </tr>
        </thead>

        <tbody>
          <tr>
          <?php
            # retrieve genome sequence
            include_once 'libphp/dbutils.php';
            connect_db();
            $query = "SELECT genome_seq FROM database_projet.genome WHERE genome_id = '" . $genome_id . "';";
            $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
            $genome_whole_seq = pg_fetch_result($result, 0, 0);
            $genome_size = strlen($genome_whole_seq);
            #echo substr($genome_whole_seq, 0, 300);

            # display first column
            echo "<td align='right'>";
            $nb_of_lines = intdiv($genome_size, $char_per_line) + 1;
            for ($line = 0; $line < $nb_of_lines; $line++) {
              $char = $line*$char_per_line + 1;
              echo "$char-<br>";
            }
            echo '</td>';
            echo "<td align='left'>";

            # extend query max time because it takes quite some time to retrieve and display whole genome
            ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
            # retrieve all genes
            $query = "SELECT sequence_id, start_seq, end_seq, gene_seq FROM database_projet.gene WHERE genome_id = '" . $genome_id . "' ORDER BY start_seq;";
            $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
            $nucl_ind_count = 0;
            $count = $char_per_line;
            for ($gene_ind = 0; $gene_ind < pg_num_rows($result); $gene_ind++) {

              # store gene informations
              $seq_id = pg_fetch_result($result, $gene_ind, 0);
              $seq_start = pg_fetch_result($result, $gene_ind, 1);
              $seq_end = pg_fetch_result($result, $gene_ind, 2);
              $gene_seq = pg_fetch_result($result, $gene_ind, 3);

              # check that sequence does not overlap with previously displayed sequences, else, skip sequence
              if ($seq_start > $nucl_ind_count) {

                # display intergenic part immediately before gene
                echo '<span style="font-family:Consolas;">'; # set style
                $seq_to_display = substr($genome_whole_seq, $nucl_ind_count, $seq_start-$nucl_ind_count-1);
                while (strlen($seq_to_display) > $count) {
                  echo substr($seq_to_display, 0, $count);
                  echo '<br>';
                  $seq_to_display = substr($seq_to_display, $count);
                  $count = $char_per_line;
                }
                echo $seq_to_display;
                $count = $count - strlen($seq_to_display);
                echo '</span>';

                echo "<a href=\"./sequence_info.php?id=" . $seq_id . "\" ";
                # check if gene is annotated
                $query_annot = "SELECT gene_id, gene_symbol, description, annotator FROM database_projet.annotations WHERE sequence_id = '" . $seq_id . "' AND genome_id = '" . $genome_id . "';";
                $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());
                # if it's not...
                if(pg_num_rows($result_annot) == 0) {
                  $color = "red";
                  $info = ' title="' . "WARNING: Unannotated gene";
                  echo 'style="font-family:Consolas;color:' . $color . ';"' . $info . '">';
                }
                # if it is...
                else {
                  $color = "blue";
                  $info = ' title="';
                  # add gene symbol if it exists
                  if (pg_fetch_result($result_annot, 0, 1) != "") {
                    $info = $info . pg_fetch_result($result_annot, 0, 1) . "\n";
                  }
                  $info = $info . pg_fetch_result($result_annot, 0, 0) . "\n" . pg_fetch_result($result_annot, 0, 2) . "\nClick to see annotation";
                  echo 'style="font-family:Consolas;color:' . $color . ';"' . $info . '">';
                }
                # display gene
                $seq_to_display = $gene_seq;
                while (strlen($seq_to_display) > $count) {
                  echo substr($seq_to_display, 0, $count);
                  echo '<br>';
                  $seq_to_display = substr($seq_to_display, $count);
                  $count = $char_per_line;
                }
                echo $seq_to_display;
                $count = $count - strlen($seq_to_display);
                echo '</a>';
                $nucl_ind_count = $seq_end;
              }
            }
            # display end of genome
            echo '<span style="font-family:Consolas;">';
            $seq_to_display = substr($genome_whole_seq, $nucl_ind_count);
            while (strlen($seq_to_display) > $count) {
              echo substr($seq_to_display, 0, $count);
              echo '<br>';
              $seq_to_display = substr($seq_to_display, $count);
              $count = $char_per_line;
            }
            echo $seq_to_display;
            $count = $count - strlen($seq_to_display);
            echo '</span>';
            echo '</td>';

            # display third column
            echo "<td align='left'>";
            $nb_of_lines = intdiv($genome_size, $char_per_line) + 1;
            for ($line = 1; $line < $nb_of_lines; $line++) {
              $char = $line*$char_per_line;
              echo "-$char <br>";
            }
            echo "-$genome_size";
            echo '</td>';
          ?>
          <td>
            <?php
              $line_ind = 0;
              $query = "SELECT sequence_id, start_seq, end_seq, gene_seq FROM database_projet.gene WHERE genome_id = '" . $genome_id . "' ORDER BY start_seq;";
              $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
              $gene_on_line = false;
              for ($gene_ind = 0; $gene_ind < pg_num_rows($result); $gene_ind++) {
                $seq_id = pg_fetch_result($result, $gene_ind, 0);
                $seq_start = pg_fetch_result($result, $gene_ind, 1);
                $gene_line = intdiv($seq_start, $char_per_line);
                while ($line_ind < $gene_line) {
                  $line_ind = $line_ind + 1;
                  $gene_on_line = false;
                  echo "<br>";
                }
                if ($gene_on_line) {
                  echo "<br>";
                  $line_ind = $line_ind + 1;
                }
                $gene_on_line = true;
                echo "<a href=\"./sequence_info.php?id=" . $seq_id . "\" ";
                echo 'style="color:blue;" title="Clik to see sequence page.">&#8592;';
                echo $seq_id;
                # check if gene has gene_symbol
                $query_annot = "SELECT gene_symbol FROM database_projet.annotations WHERE sequence_id = '" . $seq_id . "' AND genome_id = '" . $genome_id . "';";
                $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());
                # if it's not...
                if(pg_num_rows($result_annot) > 0) {
                  $gene_symb = pg_fetch_result($result_annot, 0, 0);
                  echo "($gene_symb)";
                }
                echo '</a> ';
              }
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

    Extract:
    <a href="path_to_file" download="name_file">
         <button type="button">Download</button>
         </a>

  </body>
</html>
