<?php

//>AAG54301 cds chromosome:ASM666v1:Chromosome:190:273:1 gene:Z0001
//gene_biotype:protein_coding transcript_biotype:protein_coding gene_symbol:thrL
//description:thr operon leader peptide
function read_fas_file($x) { // Check for Empty File
  if (!file_exists($x)) {
    print "This file doesn't exist. Please check the name of the file.";
    exit();
  } else {
    $fh = fopen($x, 'r');
    if (filesize($x) == 0) {
      print "This file is empty. You may want to double check where your genome is.";
      fclose($fh);
      exit();
    } else {
      $f = fread($fh, filesize($x));
      fclose($fh);
      return $f;
    }
  }
}

function fas_check($x) { // Check FASTA File Format
 $gt = substr($x, 0, 1);
 if ($gt != ">") {
  print "Unfortunetaly, this is not a fasta file.";
  exit();
 } else {
  return $x;
 }
}

function get_seq($x) { // Get Sequence and Sequence Name
 $fl = explode(PHP_EOL, $x);
 $sh = trim(array_shift($fl));
 if($sh == null) {
  $sh = "UNKNOWN SEQUENCE";
 }
 $fl = array_filter($fl);
 $seq = "";
 foreach($fl as $str) {
  $seq .= trim($str);
 }
 $seq = strtoupper($seq);
 $seq = preg_replace("/[^ACDEFGHIKLMNPQRSTVWY]/i", "", $seq);
 if ((count($fl) < 1) || (strlen($seq) == 0)) {
  print "Sequence is Empty!!";
  exit();
 } else {
  return array($sh, $seq);
 }
}

function fas_get($x) { // Read Multiple FASTA Sequences
 $gtr = substr($x, 1); //on commence à 1 et pas 0 parce que je veux pas le chevron
 $annot = explode('/\r\n|\r|\n/', $gtr); //on scinde au saut de ligne
 $seq = explode('/\r\n|\r|\n/', $annot);
 $num = substr($annot,1, 8);

 if (count($annot) > 1) {
  foreach ($annot as $sq) {
   $spair = get_seq($sq);
   $spairs[$spair[0]] = $spair[1];
  }
  return $spairs;
 } else {
  $spair = get_seq($gtr);
  return array($spair[0] => $spair[1]);
 }
}

/*foreach ($file as $truc => $list_annot) {

}*/

$file = "test_fasta.fa";
$content = read_fas_file($file);
$fasta = fas_check($content);
$seq = fas_get($fasta);
foreach($seq as $x => $y) {
  print $x;
  echo "<br>";
    echo "<br>";
  print $y;
  print "\n\n";
  echo "<br>";
  echo "<br>";
  print $num;
}
?>
