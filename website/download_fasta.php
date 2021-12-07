<?php
session_start();
include_once 'libphp/dbutils.php';
connect_db();

if ($_POST["extracted"]){

  // build text to write in file
  $file_text = '';

  $first_seq = true;

  // run through selected sequences
  foreach ($_POST['extracted_seq'] as $seq_id) {
    if (!$first_seq) {
      $file_text .= '\n;\n';
    }
    $query_gene = "SELECT genome_id, start_seq, end_seq, chromosome, gene_seq FROM database_projet.gene WHERE  sequence_id = '" . $seq_id . "';";
    $result_gene = pg_query($db_conn, $query_gene) or die('Query failed with exception: ' . pg_last_error());
    $file_text .= '>' . $seq_id . ' cds chromosome:';
    // add chromosome
    $file_text .= pg_fetch_result($result_gene, 0, 3);
    // add start and end position of sequence
    $file_text .= ':Chromosome:' . pg_fetch_result($result_gene, 0, 1) . ':' . pg_fetch_result($result_gene, 0, 2) . ':';

    // store sequence
    $seq = pg_fetch_result($result_gene, 0, 4);

    // check if annotation, if yes add it
    $query_annot = "SELECT gene_id, gene_biotype, transcript_biotype, gene_symbol, description
    FROM database_projet.annotations
    WHERE sequence_id = '" . $seq_id . "' AND status != 'rejected';";
    $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());
    if(pg_num_rows($result_annot) > 0){
      //add gene_id
      if (pg_fetch_result($result_gene, 0, 0) != "") {
        $file_text .= ' gene:' . pg_fetch_result($result_gene, 0, 0);
      }
      //add gene_biotype
      if (pg_fetch_result($result_gene, 0, 1) != "") {
        $file_text .= ' gene_biotype:' . pg_fetch_result($result_gene, 0, 1);
      }
      //add transcript_biotype
      if (pg_fetch_result($result_gene, 0, 2) != "") {
        $file_text .= ' transcript_biotype:' . pg_fetch_result($result_gene, 0, 2);
      }
      //add gene_symbol
      if (pg_fetch_result($result_gene, 0, 3) != "") {
        $file_text .= ' gene_symbol:' . pg_fetch_result($result_gene, 0, 3);
      }
      //add description
      if (pg_fetch_result($result_gene, 0, 4) != "") {
        $file_text .= ' description:' . pg_fetch_result($result_gene, 0, 4);
      }
    }
    $file_text .= '\n';
    $characters_left_on_line = 60;
    while (strlen($seq) > $characters_left_on_line) {
      $file_text .= substr($seq, 0, $characters_left_on_line);
      $seq = substr($seq, $characters_left_on_line);
      $characters_left_on_line = 60;
    }
    $file_text .= $seq;
  }


  // write and download file
  $file = "./download_files/projet_WEB.fa";
  $txt = fopen($file, "w") or die("Unable to open file!");
  fwrite($txt, $file_text);
  fclose($txt);
  header('Content-Description: File Transfer');
  header('Content-Disposition: attachment; filename='.basename($file));
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($file));
  header("Content-Type: text/plain");
  header("Connection: close");
  readfile($file);
  exit();
}

// close tab
echo  "<script type='text/javascript'>";
echo "window.close();";
echo "</script>";
?>
