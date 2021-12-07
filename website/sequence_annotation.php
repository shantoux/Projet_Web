<!-- Web page to get information about gene or protein -->
<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Annotation</title>
  <link rel="stylesheet" type="text/css" href="./style.css" /s>
</head>

  <body>
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
      Sequence Annotation
    </div>
    <?php
    include_once 'libphp/dbutils.php';
    connect_db();

    if(isset($_POST['send_annotation'])){
      //Retrieve informations
      $values_annotations = array();
      $values_annotations['gene_id'] = $_POST["gene_id"];
      $values_annotations['gene_biotype'] = $_POST["gene_biotype"];
      $values_annotations['transcript_biotype'] = $_POST["transcript_biotype"];
      $values_annotations['gene_symbol'] = $_POST["gene_symbol"];
      $values_annotations['description'] = $_POST["gene_description"];
      $values_annotations['status'] = 'waiting';

      $condition_pkey = array();
      $condition_pkey['genome_id']= $genome_id;
      $condition_pkey['sequence_id']=$sequence_id;
      $condition_pkey['annotator']=$_SESSION['user'];//$_GET['annotator'];

      $result_update = pg_update($db_conn, 'annotation_seq.annotations', $values_annotations, $condition_pkey)
      or die('Query failed with exception: ' . pg_last_error());

      if ($result_insert) {
        echo "Annotation has been set. Wait for validation.";
      } else {
        echo "Error : the annotation has not been set.";
      }
    }
      // Fill the information already in the database
      $genome_id = $_GET['gid'];
      $sequence_id = $_GET['sid'];

      $query1 = "SELECT a.genome_id, a.sequence_id FROM annotation_seq.annotations a
      WHERE a.annotator = '" . $_SESSION['user'] . "'
      AND a.genome_id = '" . $genome_id . "'
      AND a.sequence_id = '"  . $sequence_id . "';";
      $result1 = pg_query($db_conn, $query1) or die('Query failed with exception: ' . pg_last_error());
      $gid = pg_fetch_result($result1, 0, 0);
      $sid = pg_fetch_result($result1, 0, 1);

      $query2 = "SELECT gene_seq, prot_seq, start_seq, end_seq, chromosome FROM annotation_seq.gene g WHERE sequence_id = '"  . $sequence_id . "';";
      $result2 = pg_query($db_conn, $query2) or die('Query failed with exception: ' . pg_last_error());
      $nt = pg_fetch_result($result2, 0, 0);
      $prot = pg_fetch_result($result2, 0, 1);
      $start = pg_fetch_result($result2, 0, 2);
      $end = pg_fetch_result($result2,0,3);
      $chromosome = pg_fetch_result($result2,0,4);
      ?>

    <div class="center">
      <form action="./sequence_annotation.php" method = "post">
        <table class="table_type3">
          <tr colspan=2>
            <td>
            <?php
              echo "<b>Sequence identifier:</b> $sid<br><br>";
              echo "<b>Specie:</b> $gid<br>";
              echo "<b>Chromosome:</b> $chromosome<br>";
              echo "Sequence is " . strlen($nt) . " nucleotides long - it starts on position <b>" . $start . "</b> and ends on position <b>" . $end . "</b>.<br><br>";
              echo '<b>Gene identifier : </b><input type="text" required name="gene_id"><br>';
              echo '<b>Gene biotype : </b><input type="text" required name="gene_biotype"><br>';
              echo '<b>Transcript biotype : </b><input type="text" required name="transcript_biotype"><br>';
              echo '<b> Gene symbol : </b><input type ="text" required name = "gene_symbol"><br>';
              echo '<b> Description : </b><input type ="text" required name = "gene_description"><br>';
              ?>
            </td>
          </tr>
          <tr></tr>

          <tr>
            <td>
            Gene sequence<br>
            <textarea id="seq" name="seq"
            rows="8" cols="80" readonly><?php echo $nt?></textarea>
          </td>

          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $nt . "&type=nucl\" target=\"_blank\">"?>
                 <button type="button">Align with Blast</button>
                 </a></td>
        </tr>

        <tr>
          <td>
            Peptide sequence<br>
            <textarea id="seq" name="seq"
            rows="8" cols="80" readonly><?php echo $prot;?> </textarea>
          </td>
          <td>
            <?php echo "<a href=\"./libphp/blastphp.php?seq=" . $prot . "&type=prot\" target=\"_blank\">"?>
                 <button type="button">Align with Blast</button>
                 </a>
        </tr>

        <tr>
          <td align = 'center'> <input type ="submit" value="Send" name = "send_annotation"> </td>
        </tr>

      </table>
      </form>
    </div>
  </body>
</html>
