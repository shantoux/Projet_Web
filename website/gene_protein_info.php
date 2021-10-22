<!-- Web page to get information about gene or protein -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gene/Protein information </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body>
    <?php
      # TODO: un-hardcode the user role, check in database for the actual role
      $role = "administrator";
      $roles = array("annotator", "validator", "administrator");
    ?>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search_1.php">New search</a>
        <?php
          if (in_array($role, array_slice($roles, 0), true)) {
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
          }
          if (in_array($role, array_slice($roles, 1), true)) {
            echo "<a class=\"active\" href=\"./validation_1.php\">Validate annotation</a>";
          }
          if (in_array($role, array_slice($roles, 2), true)) {
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

    <div id="pagetitle">
      Gene/Protein information
    </div>

    <div class="center">
      <table class="table_type3">
        <tr colspan=2>
          <td>
          Escherichia coli - Peptide<br>
          Gene 2 : chromosome:ASM744v1:Chromosome:534:911:1<br>
          transcript : AAN78502 <br>
          gene_biotype : protein_coding <br>
          transcript_biotype : protein_coding <br>
          description : Hypothetical protein <br>
        </td>
        </tr>
        <tr>
        </tr>

        <tr>
          <td>
            Gene Sequence<br>
            <textarea id="seq" name="seq"
            rows="8" cols="60" readonly>GTGTTCTACAGAGAGAAGCGTAGAGCAATAGGCTGTATTTTGAGAAAGCTGTGTGAGTGGAAAAGTGTACGGATTCTGGAAGCTGAATGCTGTGCAGATCATATCCATATGCTTGTGGAGATCCCGCCCAAAATGAGCGTATCAGGCTTTATGGGATATCTGAAAGGGAAAAGCAGTCTGATGCCTTACGAGCAGTTTGGTGATTTGAAATTCAAATACAGGAACAGGGAGTTCTGGTGCAGAGGGTATTACGTCGATACGGTGGGTAAGAACACGGCGAAGATACAGGATTACATAAAGCACCAGCTTGAAGAGGATAAAATGGGAGAGCAGTTATCGATTCCCTATCCGGGCAGCCCGTTTACGGGCCGTAAGTAA </textarea>
          </td>
          <td>
            <a href="https://blast.ncbi.nlm.nih.gov/Blast.cgi?PROGRAM=blastn&PAGE_TYPE=BlastSearch&LINK_LOC=blasthome">
                 <button type="button">Align with Blast</button>
                 </a>
          </td>
        </tr>

        <tr>
          <td>
            Peptide Sequence<br>
            <textarea id="seq" name="seq"
            rows="8" cols="60" readonly>MFYREKRRAIGCILRKLCEWKSVRILEAECCADHIHMLVEIPPKMSVSGFMGYLKGKSSLMPYEQFGDLKFKYRNREFWCRGYYVDTVGKNTAKIQDYIKHQLEEDKMGEQLSIPYPGSPFTGRK </textarea>
          </td>
          <td>
            <a href="https://blast.ncbi.nlm.nih.gov/Blast.cgi?PROGRAM=blastp&PAGE_TYPE=BlastSearch&LINK_LOC=blasthome">
                 <button type="button">Align with Blast</button>
                 </a>
        </tr>

      </table>

      Search other websites :
      <select name="websites">
        <option value="Uniprot"> Uniprot </option>
        <option value="Embl"> Embl </option>
      </select>
      <a href="https://www.uniprot.org">
           <button type="button">Search</button>
           </a>
    </div>
  </body>
</html>
