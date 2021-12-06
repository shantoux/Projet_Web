<!-- Web page to annotate a sequence -->
<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annotation validation </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">
    <!-- display menu options depending of the user's role -->
    <div class="topnav">
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
        <a class="disc" href="login.php">Disconnect</a>
    </div>

    <h2 id="pagetitle">
      Sequence Annotation
    </h2>
    <?php
    include_once 'libphp/dbutils.php';
    connect_db();

    if(isset($_POST['submit_registration'])){
      connect_db();
      //Retrieve informations
      $values_annotations = array();
      $values_annotations['gene_id'] = $_POST["gene_id"];
      $values_annotations['gene_biotype'] = $_POST["gene_biotype"];
      $values_annotations['transcript_biotype'] = $_POST["transcript_biotype"];
      $values_annotations['gene_symbol'] = $_POST["gene_symbol"];
      $values_annotations['gene_description'] = $_POST["gene_description"];

      $result_insert = pg_insert($db_conn, 'annotation_seq.annotations', $values_annotations);

      $condition_pkey = array();
      $condition_pkey['genome_id']=$_GET['gid'];
      $condition_pkey['sequence_id']=$_GET['sid'];
      $condition_pkey['annotator']=$_GET['annotator'];

      $result_insert = pg_update($db_conn, 'annotation_seq.users', $values_annotations, $condition) or die('Query failed with exception: ' . pg_last_error());


      if ($result_insert) {
        echo "<div class=\"alert_good\">
                <span class=\"closebtn\"
                onclick=\"this.parentElement.style.display='none';\">&times;</span>
                Annotation successfully saved, wait for validation by an admin.
              </div>";
            } else {
              echo "<div class=\"alert_bad\">
              <span class=\"closebtn\"
              onclick=\"this.parentElement.style.display='none';\">&times;</span>
              Error during registration.
              </div>";
            }
          }

    ?>
    <div class = "table_type1">
      <?php
      echo '<form action="./sequence_annotation.php" method = "post">';
      echo '<table><thead><tr>';
      echo '<th>Sequence</th>';
      echo '<th>Annotation</th>';
      echo '</tr></thead>';

      echo '<tbody>';

      echo '<tr>';

      //retrieve info
      $genome_id = $_GET['gid'];
      $sequence_id = $_GET['sid'];

      $query1 = "SELECT a.genome_id, a.sequence_id FROM annotation_seq.annotations a
      WHERE a.annotator = '" . $_SESSION['user'] . "'
      AND a.genome_id = '" . $genome_id . "'
      AND a.sequence_id = '"  . $sequence_id . "';";
      $result1 = pg_query($db_conn, $query1) or die('Query failed with exception: ' . pg_last_error());
      $gid = pg_fetch_result($result1, 0, 0);
      $sid = pg_fetch_result($result1, 0, 1);

      $query2 = "SELECT gene_seq FROM annotation_seq.gene g WHERE sequence_id = '"  . $sequence_id . "';";
      $result2 = pg_query($db_conn, $query2) or die('Query failed with exception: ' . pg_last_error());
      $sequence = pg_fetch_result($result2, 0, 0);

      echo '<td> Genome identifier : ';
      echo $gid;
      echo '<br> Sequence identifier : ';
      echo $sid;
      echo '<br>Sequence : ';
      echo '<textarea id="seq" name="seq" rows="8" cols="80" readonly>';
      echo $sequence;
      echo '</textarea>';
      echo '</td>';

      echo '<td>';
      echo 'Gene identifier : <input type="text" name="gene_id"><br>';
      echo 'Gene biotype : <input type="text" name="gene_biotype"><br>';
      echo 'Transcript Biotype : <input type="text" name="transcript_biotype"><br>';
      echo 'Gene symbol : <input type="text" name="gene_symbol"><br>';
      echo 'Description : <input type="text" name="gene_description"><br></td></tr>';

      echo '<tr><td colspan=2> <input type ="submit" value="Save" name = "submit_annotation"> </td>';
      echo '<td colspan=2> <input type ="submit" value="Send" name = "submit_annotation"> </td></tr>';
      echo '</tbody></table></div>';

      echo "<a href=\"./libphp/blastphp.php?seq=" . $sequence . "&type=nucl\" target=\"_blank\">"?>
        <button type="button">Align with Blast</button>
      </a>


    </form>
  </body>
</html>
