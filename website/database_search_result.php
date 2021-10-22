<!DOCTYPE html>
<html>
<head> <meta charset="UTF-8">
  <title>Database search result</title>
  <link rel="stylesheet" type="text/css" href="pw_style.css" />
</head>

<body class="center">
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
          echo "<a href=\"./validation_1.php\">Validate annotation</a>";
        }
        if (in_array($role, array_slice($roles, 2), true)) {
          echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
        }
      ?>
      <a href="about.php">About</a>
      <a class="disc" href="Login_page1.php">Disconnect</a>
  </div>
  <br>

  <div id="pagetitle">
    Search results
  </div>

  Genome's type : [insert type protein or nucleotides]<br>
  <div id="element1">Results</div>

  <div class = "center">
    <table class = "table_soun">

    <thead>
        <tr>
            <th>Genome</th>
            <th>Species</th>
            <th>Sequences</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td><?php echo "<a href=\"./genome_info.php\">Genome example1</a>"; ?>
            </td>

            <!--clickable : sends you to genome info page-->

            <td>Escherichia coli </td>
            <td>ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCG
GGCTGA</td>
        </tr>
        <tr>
            <td>
              <?php echo "<a href=\"./genome_info.php\">Genome example2</a>"; ?>
            </td>
            <td>Escherichia coli</td>
            <td>GTGTTCTACAGAGAGAAGCGTAGAGCAATAGGCTGTATTTTGAGAAAGCTGTGTGAGTGG
AAAAGTGTACGGATTCTGGAAGCTGAATGCTGTGCAGATCATATCCATATGCTTGTGGAG
ATCCCGCCCAAAATGAGCGTATCAGGCTTTATGGGATATCTGAAAGGGAAAAGCAGTCTG
ATGCCTTACGAGCAGTTTGGTGATTTGAAATTCAAATACAGGAACAGGGAGTTCTGGTGC
AGAGGGTATTACGTCGATACGGTGGGTAAGAACACGGCGAAGATACAGGATTACATAAAG
CACCAGCTTGAAGAGGATAAAATGGGAGAGCAGTTATCGATTCCCTATCCGGGCAGCCCG
TTTACGGGCCGTAAGTAA</td>
        </tr>
        <tr>
            <td>
              <?php echo "<a href=\"./genome_info.php\">Genome example3</a>"; ?>
            </td>
            <td>Escherichia coli</td>
            <td>ATGC</td>
        </tr>

        <tbody>

  </table>
</div>
</body>
</html>
