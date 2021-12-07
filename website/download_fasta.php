<?php
session_start();
include_once 'libphp/dbutils.php';
connect_db();

if ($_POST["extracted"]){

  // build text to write in file
  $file_text = '';

  // run through selected sequences
  foreach ($_POST['extracted_seq'] as $seq_id) {
    $query_seq = "SELECT genome_id, start_seq,end_seq, chromosome, gene_seq FROM database_projet.annotations WHERE genome_id = '" . $g_id . "' AND sequence_id = '" . $s_id . "' AND status != 'rejected';";
    $result_annot = pg_query($db_conn, $query_annot) or die('Query failed with exception: ' . pg_last_error());
    $new_conv_member['topic_name'] = $_POST['topic_name'];
    $new_conv_member['user_email'] = $user_email;
    $result_insert_2 = pg_insert($db_conn, 'database_projet.correspondents', $new_conv_member);
  }


  // write and download file
  $file = "projet_WEB.fa";
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
