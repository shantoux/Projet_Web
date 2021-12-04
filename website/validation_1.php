<!-- Web page to validate sequence annotations -->
<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annotation validation </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body class="center">
    <?php
      # TODO: un-hardcode the user role, check in database for the actual role
      $role = "administrator";
      $roles = array("annotator", "validator", "administrator");
    ?>

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

    <h2 id="pagetitle"> Annotations waiting for validation </h2>

    <!-- TODO: retrieve anotations from the database. The following is hardcoded data to display pages in the meantime. -->
    <?php
      $annotations = array();
      $annot_file = file_get_contents('./annotations.txt');
      $rows = explode("\n", $annot_file);

      foreach($rows as $row => $data){
        //get row data
        $row_data = explode(',', $data);
        $annotation = array();

        array_push($annotation, $row_data[0]);
        array_push($annotation, $row_data[1]);
        array_push($annotation, $row_data[2]);
        array_push($annotation, $row_data[3]);

        array_push($annotations, $annotation);
      }
    ?>

    <!-- TODO: Here we remove the annotations from the list because they have been validated or refused.
    But at a point we will have to update their status in the database - they just will not be loaded afterwards. -->

    <?php
      if(isset($_POST['validate']) || isset($_POST['refuse'])){
        $id = $_POST['annotation_id'];
        array_splice($annotations,$id-1,1);
        $new_annot = "";

        foreach ($annotations as $key1 => $annotation) {
          foreach ($annotation as $key2 => $value) {
            if ($key2 != 0) {$new_annot = $new_annot . $value . ",";}
            # Update the new id of each annotation, in the text and in the annotation array.
            else {
              $new_annot = $new_annot . "\"" . (string)($key1+1) . "\",";
              $annotations[$key1][0] = (string)($key1+1);
            }
          }
          $new_annot = substr($new_annot, 0, -1) . "\n";
        }
        $new_annot = substr($new_annot, 0, -1);

        $annot_file = fopen("./annotations.txt", "w") or die("Unable to open file!");
        fwrite($annot_file, $new_annot);
        fclose($annot_file);
      }
    ?>

    <!-- Table to display annotations waiting for validation -->

    <div class = "table_type1">
      <table>
        <colgroup>
          <col style="width: 13%">
          <col style="width: 25%">
          <col style="width: 10%">
          <col style="width: 15%">
          <col style="width: 18%">
          <col style="width: auto">
        </colgroup>
        <thead>
          <tr>
            <th>Submission date</th>
            <th>Sequences</th>
            <th>Annotator</th>
            <th>Comments</th>
            <th colspan=2>Action</th>
          </tr>
        </thead>

        <tbody>
          <?php
            # Print a new line for each available annotation.
            foreach ($annotations as $annotation) {
              echo "<tr>";
              # Annotation submission date
              echo "<td>" . $annotation[1] . "</td>";
              # Annotation sequence (cut)
              echo "<td>" . substr($annotation[2], 0, 25);
              if (strlen($annotation[2]) > 25) {
                echo "...";
              }
              echo "</td>";
              # Annotator
              echo "<td>" . $annotation[3] . "</td>";
              # Comment for validation or refusal
              echo "<td> <input type=\"text\" name=\"comments\"> </td>";
              # Review annotation
              echo "<td> <form action=\"annotation_2.php\" method = \"post\">";
              echo "<input type=\"submit\" value=\"Review annotation\" name=\"review\"> </form> </td>";
              # Validate / Refuse annotation
              echo "<td>";
              echo "<div style=\"float:left; width: 50%;\"> <form action=\"" . $_SERVER['PHP_SELF'] . "\" method = \"post\">";
              echo "<input type=\"text\" value=" . $annotation[0] . " name=\"annotation_id\" hidden>";
              echo "<input type=\"submit\" value=\"Validate\" name=\"validate\"> </form> </div>";
              echo "<div style=\"float: left; width: auto;\"> <form action=\"" . $_SERVER['PHP_SELF'] . "\" method = \"post\">";
              echo "<input type=\"text\" value=" . $annotation[0] . " name=\"annotation_id\" hidden>";
              echo "<input type=\"submit\" value=\"Refuse\" name=\"refuse\"> </form> </div>";
              echo "</td>";
              echo "</tr>";
            }
          ?>
        <tbody>
      </table>
    </div>
  </body>
</html>
