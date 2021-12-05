<!-- Web page to attribute annotation to annotator -->
<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sequence attribution </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body class="center">
    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a class="active" href="./search_1.php">New search</a>
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

    <h2  id="pagetitle"> Sequences to attribute </h2>

    <?php
      include_once 'libphp/dbutils.php';
      connect_db();
    ?>
    <br>

    <!-- Display table of results for the search -->
    <div id="element1"> <!-- <div class = "center"> -->
      <?php
        echo '<table class = "table_type1">';

        # display first line
        echo '<thead>';
        echo '<tr>';
        echo '<th>Genome</th>';
        echo '<th>Sequence</th>';
        echo '<th>Annotators</th>';
        echo '<th>-------</th>';
        echo '</tr>';
        echo '</thead>'; #end of first line

        # display results of search
        echo '<tbody>';
        $seq_attribution="SELECT G.genome_id, E.sequence_id
        FROM annotation_seq.genome G, annotation_seq.gene E
        WHERE G.genome_id = 'new_coli';";

        /*$list_annotator="SELECT U.first_name, U.last_name
        FROM annotation_seq.users U
        WHERE U.role='annotator';";*/
        $list_annotator="SELECT email FROM annotation_seq.users U WHERE U.role='annotator';";

        $result1 = pg_query($db_conn, $seq_attribution) or die('Query failed with exception: ' . pg_last_error());
        $result2 = pg_query($db_conn, $list_annotator) or die('Query failed with exception: ' . pg_last_error());

        if(pg_num_rows($result1) > 0){
          for ($res_nb = 0; $res_nb < pg_num_rows($result1); $res_nb++) {
            $genome_id = pg_fetch_result($result1, $res_nb, 0); //récupère le résultat de la 1e colonne (0), $res_nb ieme ligne ($res_nb)
            $sequence_id = pg_fetch_result($result1, $res_nb, 1); //récupère le résultat de la 2e colonne (0), $res_nb ieme ligne ($res_nb)
            echo '<tr><td>';
            echo $genome_id;
            echo '</td><td>';
            echo $sequence_id;
            echo '</td>';
            echo '<td><select name="selected_annotator">';

            if (pg_num_rows($result2)>0){
              $nb_rows = pg_num_rows($result2);
              for($res2_nb = 0; $res2_nb < pg_num_rows($result2); $res2_nb++){
                //$annotator_first_name= pg_fetch_result($result2, $res2_nb, 0); //récupère le résultat de la 1e colonne (0), $res_nb ieme ligne ($res_nb)
                //$annotator_last_name= pg_fetch_result($result2, $res2_nb, 1); //récupère le résultat de la 2e colonne (0), $res_nb ieme ligne ($res_nb)
                $annotator_email= pg_fetch_result($result2, $res2_nb, 0); //récupère le résultat de la 2e colonne (0), $res_nb ieme ligne ($res_nb)
                echo '<option value="annotator">';
                echo $annotator_email;
                //echo $annotator_first_name." ". $annotator_last_name;
                echo '</option>';
              }
              echo '</select><input type="submit" value="Attribute"></td>';
            }
            if(isset($_POST['Attribute'])){
              if(!empty($_POST["selected_annotator"])){
                $values_annotations = array();
                $values_annotations['genome_id'] = $genome_id;
                $values_annotations['sequence_id'] = $sequence_id;
                $values_annotations['annotator'] = $_POST["selected_annotator"];
                $result_insert = pg_insert($db_conn, 'annotation_seq.annotations', $values_annotations);
                  //$annotatoremail = $_POST["selected_annotator"]; //Retrieve information
                  //$get_email = "SELECT email FROM annotation_seq.users u WHERE u.last_name ='$annotatorlastname';"; //AND u.first.name ='$annotator_first_name';";
                  //$result = pg_query($db_conn, $get_email) or die('Query failed with exception: ' . pg_last_error());
                  //$email = pg_fetch_result($result, 0, 0);
                  //$query = "INSERT INTO annotation_seq.annotations(genome_id, sequence_id, annotator) VALUES ('$genome_id','sequence_id','$annotator_email');";
                  //$instances = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());
                if ($result_insert) {
                  echo "<td> Successfully added</td>";
                } else {
                  echo "<td> Not added</td>";
                }
              }
            }
            echo '</tr>';
          }
        }
        else{
          echo "<div class=\"alert_bad\">
          <span class=\"closebtn\" onclick=\"this.parentElement.style.display='none';\">&times;</span>
          There is no new sequences to attribute.</div>";
        }
        echo '</tbody>';
        echo '</table>';
        ?>
      </div>

  </body>

  <?/*php
  if(isset($_POST['Attribute'])){
    if($db_conn) {
      echo 'connected';
    }
    else {
      echo 'there has been an error connecting';
    }
    if(!empty($_POST["selected_annotator"])){
      $annotatorlastname = $_POST["selected_annotator"]; //Retrieve information
      $get_email = "SELECT email FROM annotation_seq.users u WHERE u.last_name ='$annotator_last_name' AND u.first.name ='$annotator_first_name';";
      $result = pg_query($db_conn, $get_email) or die('Query failed with exception: ' . pg_last_error());
      $email = pg_fetch_result($result, 0, 0);
      $query = "INSERT INTO annotation_seq.annotations(annotator) VALUES ('$email');";
      $instances = pg_query($db_conn, $query);
    }
  }
    */?>
</html>
