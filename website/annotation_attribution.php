<!-- Web page to attribute annotation to annotator -->
<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sequence attribution </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">
    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a href="./search.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a class=\"active\" href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
    </div>

    <h2  id="pagetitle"> Sequences to attribute </h2>

    <?php
      include_once 'libphp/dbutils.php';
      connect_db();

      # attribute annotation
      if(isset($_POST["selected_annotator"])){
        $values_annotations = array();
        $values_annotations['genome_id'] = $_GET['gid'];
        $values_annotations['sequence_id'] = $_GET['sid'];
        $values_annotations['annotator'] = $_POST["selected_annotator"]; //annotator_email;
        $values_annotations['tries'] = '0';

        $result_insert = pg_insert($db_conn, 'database_projet.annotations', $values_annotations);
        if ($result_insert) {
          echo "<td> Successfully added</td>";

          $to = $_POST["selected_annotator"]; // Send email to our user
          $subject = "A new annotation is waiting for you"; // Give the email a subject
          $emessage = "A new sequence is waiting for you to annotate it !";

          // if emessage is more than 70 chars
          $emessage = wordwrap($emessage, 70, "\r\n");

          // Our emessage above including the link
          $headers   = array();
          $headers[] = "MIME-Version: 1.0";
          $headers[] = "Content-type: text/plain; charset=iso-8859-1";
          $headers[] = "From: no-reply <noreply@yourdomain.com>";
          $headers[] = "Subject: {$subject}";
          $headers[] = "X-Mailer: PHP/".phpversion(); // Set from headers

          mail($to, $subject, $emessage, implode("\r\n", $headers));
        } else {
          echo "<td> Not added</td>";
        }
      }
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
        echo '</tr>';
        echo '</thead>'; #end of first line

        # display results of search
        echo '<tbody>';
        $seq_attribution="SELECT G.genome_id, E.sequence_id
        FROM database_projet.genome G, database_projet.gene E
        WHERE G.genome_id = E.genome_id
        EXCEPT (SELECT A.genome_id, A.sequence_id FROM database_projet.annotations A);";

        $list_annotator="SELECT U.first_name, U.last_name, U.email
        FROM database_projet.users U
        WHERE U.role='annotator';";

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
            echo '<td><form action="./annotation_attribution.php?gid=' . $genome_id . '&sid=' . $sequence_id . '" method="post"><select name="selected_annotator">';

            if (pg_num_rows($result2)>0){
              for($res2_nb = 0; $res2_nb < pg_num_rows($result2); $res2_nb++){
                $annotator_first_name= pg_fetch_result($result2, $res2_nb, 0); //récupère le résultat de la 1e colonne (0), $res_nb ieme ligne ($res_nb)
                $annotator_last_name= pg_fetch_result($result2, $res2_nb, 1); //récupère le résultat de la 2e colonne (0), $res_nb ieme ligne ($res_nb)
                $annotator_email= pg_fetch_result($result2, $res2_nb, 2); //récupère le résultat de la 2e colonne (0), $res_nb ieme ligne ($res_nb)
                echo '<option value="'. $annotator_email . '">';
                echo $annotator_first_name." ". $annotator_last_name;
                echo '</option>';
              }
              echo '</select><input type="submit" value="Attribute" name="Attribute"></td></form>';
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
</html>
