<!-- Web page to attribute annotation to annotator -->
<?php session_start();?>

<!DOCTYPE html>
<html>
  <!-- Page header -->
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

      // attribute annotation
      if(isset($_POST["selected_annotator"])){
        $values_annotations = array(); // List of columns to fill in the annotations table : all the primary keys
        $values_annotations['genome_id'] = $_GET['gid'];
        $values_annotations['sequence_id'] = $_GET['sid'];
        $values_annotations['annotator'] = $_POST["selected_annotator"]; //annotator's email;
        $values_annotations['attempt'] = '0'; // Set the number of annotation attempt to 0 when the sequence is attributed

        // Insert in the annotations table
        $result_insert = pg_insert($db_conn, 'database_projet.annotations', $values_annotations);
        if ($result_insert) {
          // If the insertion was done successfully : print a message informing the user and send an email to the annotator
          echo "<td> Attribution successfully added.</td>";

          // Send email to the annotator to inform them they were attributed a new sequence

          $to = $_POST["selected_annotator"];  // Get the annotator's email
          $subject = "A new annotation is waiting for you"; // Email subject
          $emessage = "A new sequence has been attributed to you!\n
          Sequence identifier : '.$_GET['gid'].'
          Genome identifier : $_GET['gid']
          Thank you for your contribution.";

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
          // If the insertion failed : print a message to inform the user
          echo "<td> Attribution NOT added.</td>";
        }
      }
    ?>


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

        // Query to only get the un-annotated sequences :
        // retrieve all the sequences except for the one already in the annotations table
        $seq_attribution="SELECT G.genome_id, E.sequence_id
        FROM database_projet.genome G, database_projet.gene E
        WHERE G.genome_id = E.genome_id
        EXCEPT (SELECT A.genome_id, A.sequence_id FROM database_projet.annotations A);";

        // Query to get the names and email of the annotators
        $list_annotator="SELECT U.first_name, U.last_name, U.email
        FROM database_projet.users U
        WHERE U.role='annotator';";

        // Execute the queries
        $result1 = pg_query($db_conn, $seq_attribution) or die('Query failed with exception: ' . pg_last_error());
        $result2 = pg_query($db_conn, $list_annotator) or die('Query failed with exception: ' . pg_last_error());


        if(pg_num_rows($result1) > 0){
          // If there is un-annotated sequences
          for ($res_nb = 0; $res_nb <= pg_num_rows($result1); $res_nb++) {
            // Loop over each un-annotated sequences
            $genome_id = pg_fetch_result($result1, $res_nb, 0); //get the result of the first column (0) for the row in question
            $sequence_id = pg_fetch_result($result1, $res_nb, 1);

            # display results of query
            echo '<tr><td>';
            echo $genome_id;
            echo '</td><td>';
            echo $sequence_id;
            echo '</td>';
            echo '<td><form action="./annotation_attribution.php?gid=' . $genome_id . '&sid=' . $sequence_id . '" method="post"><select name="selected_annotator">';

            if (pg_num_rows($result2)>0){
              // If there is at least 1 annotator available in the database (and for each sequence we are looping over)
              for($res2_nb = 0; $res2_nb < pg_num_rows($result2); $res2_nb++){
                // Loop over each annotator
                $annotator_first_name= pg_fetch_result($result2, $res2_nb, 0); //get the result of the first column (0) for the row in question
                $annotator_last_name= pg_fetch_result($result2, $res2_nb, 1);
                $annotator_email= pg_fetch_result($result2, $res2_nb, 2);

                # display results of query
                echo '<option value="'. $annotator_email . '">';
                echo $annotator_first_name." ". $annotator_last_name;
                echo '</option>';

              } // exit the loop over the annotator list

              # display button to choose which annotator will annotate the sequence
              echo '</select><input type="submit" value="Attribute" name="Attribute"></td></form>';

            } // exit the if condition for the presence of annotator in the database
            echo '</tr>';

          } // exit the loop over un-annotated sequences

        } // exit the if condition for the presence of un-annotated sequences in the database
        else {
          // if all the sequences in the database are annotated

          # display message
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
