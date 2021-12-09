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

  <body>

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
        <a class="disc"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
    </div>

    <!-- Display page title -->
    <div id="pagetitle">
      Gene/Protein information
    </div>

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
      <table class="table_type3">
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
            <textarea id="seq" name="seq"
            rows="8" cols="80" readonly><?php echo $gene_seq;?></textarea>
          </td>

          <!-- display button for automative blast alignment of the nucleotidic sequence -->
          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $gene_seq . "&type=nucl\" target=\"_blank\">"?>
                 <button type="button">Align with Blast</button>
                 </a>
          </td>
        </tr>

        <!-- Display peptidic sequence -->
        <tr>
          <td>
            Peptide sequence<br>
            <textarea id="seq" name="seq"
            rows="8" cols="80" readonly><?php echo $prot_seq;?> </textarea>
          </td>

          <!-- display button for automative blast alignment of the peptidic sequence -->
          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $prot_seq . "&type=prot\" target=\"_blank\">"?>
                 <button type="button">Align with Blast</button>
                 </a>
        </tr>

      </table>

      <!-- Display button to search in other bases with the sequence identifier -->
      Search other websites :
      <?php echo '<form action="sequence_info.php?sid=' . $seq_id . '" method="post" target="blank">';?>
      <select name="websites">
        <option value="Uniprot"> Uniprot </option>
        <option value="Embl"> Embl </option>
      </select>
      <input type="submit" name="search" value="Search">
    </form>
    </div>

    <?php

    include_once 'libphp/simplehtmldom/simple_html_dom.php';
    $adress = 'https://www.uniprot.org/uniprot/?query=' . $seq_id . '&sort=score';
    $html = file_get_html($adress);
    $uniprot_protein_name = $html->find(".entryID", 0)->plaintext;

    $adress = 'https://pfam.xfam.org/protein/' . $uniprot_protein_name;

    echo '<div class="center">';
    echo $adress . '<br>';
    echo '<form action="' . $adress . '" method="post" target="blank">';
    echo '<input type="submit" name="reach_uni" value="du_tres_tres_sale">';
    echo '</form>';

    echo '<br>';
    $lines_of_interest = file_get_html($adress)->find("table#imageKey.resultTable.details", 0)->plaintext;
    echo $lines_of_interest . '<br>';
    $lines_of_interest = file_get_html($adress)->find("table#imageKey.resultTable.details", 0)->children(1)->children(1)->plaintext;
    echo $lines_of_interest . '<br>';

    echo '<br><br>';

    $t = file_get_html($adress)->find("table#imageKey.resultTable.details", 0)->children(1);
    $try[] = $t;

    echo '<br><br>';

    echo $t->children(1)->children(1)->plaintext . '<br>';
    echo $t->children(1)->children(2)->plaintext . '<br>';
    echo $t->children(1)->children(3)->plaintext . '<br>';

    echo '<br><br>';

    echo file_get_html($adress)->getElementsByClassName("resultTable details")->childNodes(1)->childElementCount;


    echo '</div>';

    ?>

  </body>
</html>
<!--
PfamPALP8337235.0035.0085.3084.805.7e-208.1e-20


$try[] = $t->children(1); ########### Ca c'est le premier <tr class="odd">
Array (
  [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => tr [attributes] => Array ( [class] => odd )
    [nodes] => Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => pfama_PF00291 ) [nodes] => none )
  [1] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none
    [nodes] => Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => a [attributes] => Array ( [href] => /family/PALP ) [nodes] => none ) ) )
  [2] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none [nodes] => none )
  [3] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none [nodes] => none )
  [4] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => sh [style] => display: none ) [nodes] => none )
  [5] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => sh [style] => display: none ) [nodes] => none )
  [6] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => sh [style] => display: none ) [nodes] => none )
  [7] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => sh [style] => display: none ) [nodes] => none )
  [8] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => sh [style] => display: none ) [nodes] => none )
  [9] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => sh [style] => display: none ) [nodes] => none ) ) ) )

  $try[] = $t; ############# Ca c'est le tbody
  print_r($try);

  Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => tbody [attributes] => none
              [nodes] => Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => tr [attributes] => Array ( [class] => odd )
                                        [nodes] => Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => pfama_PF14821 ) [nodes] => none )
                                                          [1] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none
                                                                    [nodes] => Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => a [attributes] => Array ( [href] => /family/Thr_synth_N ) [nodes] => none ) ) )
                                                          [2] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none [nodes] => none )
                                                          [3] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none [nodes] => none ) ) )
                                  [1] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => tr [attributes] => Array ( [class] => odd )
                                        [nodes] => Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => Array ( [class] => pfama_PF00291 ) [nodes] => none )
                                                          [1] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none
                                                                      [nodes] => Array ( [0] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => a [attributes] => Array ( [href] => /family/PALP ) [nodes] => none ) ) )
                                                          [2] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none [nodes] => none )
                                                          [3] => simplehtmldom\HtmlNode Object ( [nodetype] => HDOM_TYPE_ELEMENT (1) [tag] => td [attributes] => none [nodes] => none ) ) ) ) ) )






-->
