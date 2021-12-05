<!-- Web page to get information about genome -->
<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Genome information </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search_1.php">New search</a>
        <?php
          if ($_SESSION['status'] == 'annotator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
          }
          if ($_SESSION['status'] == 'validator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
          }
          if ($_SESSION['status'] == 'administrator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

    <div id="pagetitle">
      Genome information
    </div>

    <!-- store genome -->
    <?php
      # initialize a variable for the number of characters to display per line
      $char_per_line = 100;
      if (isset($_POST["nb_nucl_per_line"])) {
        $char_per_line = $_POST["nb_nucl_per_line"];
      }
      # retrieve genome informations
      $genome_id = $_GET['id'];
      # retrieve genome sequence
      include_once 'libphp/dbutils.php';
      connect_db();
      $query = "SELECT genome_seq FROM annotation_seq.genome WHERE genome_id = '" . $genome_id . "';";
      $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
      $genome_whole_seq = pg_fetch_result($result, 0, 0);
      #echo substr($genome_whole_seq, 0, 300);
      # retrieve all genes
      $genome_fragments = array()
      $query = "SELECT sequence_id, start_seq, end_seq, gene_seq FROM annotation_seq.gene WHERE genome_id = '" . $genome_id . "';";
      $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
      $nucl_ind_count = 0
      for ($gene_ind = 0; $gene_ind < pg_num_rows($result); $gene_ind++) {
        $seq_id = pg_fetch_result($result, $gene_ind, 0);
        $seq_start = pg_fetch_result($result, $gene_ind, 1);
        $seq_end = pg_fetch_result($result, $gene_ind, 2);
        $gene_seq = pg_fetch_result($result, $gene_ind, 3);
        # add intergene before the gene to the fragments lists
        array_push($genome_fragments, array("seq"=>substr($genome_whole_seq, $nucl_ind_count, $seq_start-1), "type"=>"igene", "id"=>$gene_ind));
        # check if gene is annotated
        # add gene to gene fragments
        array_push($genome_fragments, array("seq"=>$gene_seq, "type"=>"gene", "id"=>$gene_ind));
        $nucl_ind_count = $seq_end + 1;
      }



      $sequences = array("CGATCGATGAGCAGCTTTGCATGCAGAAACGATCGGCGCGCTAGTACGCCCGGCTGCATGCAGAAACGATCGGCGCGCTAGTACGATCGTCAGGATCACTACGCAGCACTAGC",
                          "ATGCGTACGATCGTGACATCTGATCGTCTCTAGCTAGCATCTGGCATCG",
                          "GCTCGGGATACGCTCAGCTGGAGCCTGGCTATCATGCGAGCTAGGC",
                          "ATGCAGTGAGCGCGATCGAGACGCTGATGATCGTAGACGTCGA",
                          "CGATCGATGAGCAGCTTCCCGGCTGCATGCAGAAACGATCGGCGCGCTAGTACGATCGTCAGGATCACTACGCAGCACTAGC",
                          "ATGCGATGCAATCTGCTAGACAGCTACGC");
      $genome_fragments = array(array("seq"=>$sequences[0], "type"=>"igene", "id"=>"0"),
                                array("seq"=>$sequences[1], "type"=>"gene", "id"=>"1", "annotated"=>false, "name"=>"PHO1"),
                                array("seq"=>$sequences[2], "type"=>"igene", "id"=>"1"),
                                array("seq"=>$sequences[3], "type"=>"gene", "id"=>"2", "annotated"=>true, "info"=>"That's a very uncommon gene.", "name"=>"PHO2"),
                                array("seq"=>$sequences[4], "type"=>"igene", "id"=>"2"),
                                array("seq"=>$sequences[5], "type"=>"gene", "id"=>"3", "annotated"=>true, "info"=>"That's the 6th finger gene.", "name"=>"PHO3"));
      echo "<br>";
      $genome_size_to_unhardcode_later = 0;
      foreach ($sequences as $seq) {
        $genome_size_to_unhardcode_later += strlen($seq);
      }
    ?>

    <div class="center">
      <table class="table_type_gene_inf">
        <colgroup>
          <col span="1" style="width: 10%;">
          <col span="1" style="width: 70%;">
          <col span="1" style="width: 10%;">
          <col span="1" style="width: 10%;">
        </colgroup>
        <thead>
          <tr>
            <th colspan=2 class="type2"  align='left'>Genome's name : Ecoli</th>
            <th colspan=2 text-align='right' horizontal-align="middle">
              <form action="genome_info.php" method="post">
                Nb of nucl. per line:
                <input type="text" name="nb_nucl_per_line" maxlength="4" size="4" value="<?php echo $char_per_line; ?>">
                <input type="submit" value="Update">
              </form>
            </th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td align='right'>
              <?php
                $genome_size = $genome_size_to_unhardcode_later;
                $nb_of_lines = intdiv($genome_size, $char_per_line) + 1;
                for ($line = 0; $line < $nb_of_lines; $line++) {
                  $char = $line*$char_per_line + 1;
                  echo "$char-<br>";
                }
              ?>
            </td>
            <td align='left'>
              <?php
                $count = $char_per_line;
                foreach ($genome_fragments as $fragment) {
                  if ($fragment["type"] == "igene") {
                    echo '<span style="font-family:Consolas;">';
                  }
                  else {
                    if ($fragment["annotated"]) {
                      $color = "blue";
                      $info = ' title="' . $fragment["name"] . "\n" . $fragment["info"] . "\nClick to see annotation";
                      echo '<span style="font-family:Consolas;color:' . $color . ';"' . $info . '">';
                    }
                    else {
                      $color = "red";
                      $info = ' title="' . "WARNING: Unannotated gene";
                      echo '<span style="font-family:Consolas;color:' . $color . ';"' . $info . '">';
                    }
                  }
                  $seq_to_display = $fragment["seq"];

                  while (strlen($seq_to_display) > $count) {
                    echo substr($seq_to_display, 0, $count);
                    echo '<br>';
                    $seq_to_display = substr($seq_to_display, $count);
                    $count = $char_per_line;
                  }
                  echo $seq_to_display;
                  $count = $count - strlen($seq_to_display);

                  echo '</span>';
                }
              ?>
            </td>
            <td align='left'>
              <?php
                $genome_size = $genome_size_to_unhardcode_later;
                $nb_of_lines = intdiv($genome_size, $char_per_line) + 1;
                for ($line = 1; $line < $nb_of_lines; $line++) {
                  $char = $line*$char_per_line;
                  echo "-$char <br>";
                }
                echo "-$genome_size";
              ?>
            </td>
            <td>
              <?php
                $count = 0;
                for ($seq_ind = 0; $seq_ind < sizeof($sequences); $seq_ind++) {
                  $char = $line*$char_per_line + 1;
                  #echo "$char <br>";
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
