<!-- Web page to get information about genome -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Genome information </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body>
    <?php
      $char_per_line = 50
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
          if (in_array($role, array_slice($roles, 1), true)) {
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
    ?>

    <div class="center">
      <table class="table_type_gene_inf">
        <colgroup>
          <col span="1" style="width: 10%;">
          <col span="1" style="width: 90%;">
        </colgroup>
        <thead>
          <tr>
            <th colspan=2 class="type2">Genome's name : Ecoli</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>
              extract
              <input type="checkbox" id="Select" name="select">
            </td>
            <td>
              <?php
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
                  print_r($fragment["seq"]);
                  echo '</span>';
                  #print_r($fragment[0]);
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
