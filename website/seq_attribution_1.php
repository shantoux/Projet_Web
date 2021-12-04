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

    <div class = "center">
      <table class="table_type1">
        <thead>
          <tr>
            <th>Sequences</th>
            <th>Genome</th>
            <th>Annotator</th>
          </tr>
        </thead>

        <tbody>
          <?php
          include_once 'libphp/dbutils.php';

          if(isset($_POST['submit'])){
          //essai connexion postgres
            connect_db();
          $seq_attribution="SELECT G.genome_id, U.first_name, U.last_name
          FROM genome G, users U, annotations A
          WHERE G.genome_id = A.genome_id
          AND U.role='annotator';";

          $result = pg_query($db_conn, $seq_attribution)
    					or die('Query failed with exception: ' . pg_last_error());

          $_SESSION['first_name'] = pg_fetch_result($result, 0, 1);
          $_SESSION['last_name'] = pg_fetch_result($result, 0, 2);
          ?>
          <tr>
            <td>ATGAAACGCATTAGCACCACCATTACCACCACCATCACCATTACCACAGGTAACGGTGCGGGCTGA </td>
            <td> Ecoli</td>
            <td>
              <select name="annotator">
                <option value="annotator"> <?php $_SESSION['first_name'] $_SESSION['last_name'] ;?></option>
                <!--<option value="annotator"> <?php $_SESSION['first_name'] $_SESSION['last_name'] ;?> </option>-->
              </select>

              <input type="submit" value="Choose">
            </td>
          </tr>

        <tbody>
      </table>
    </div>
  </body>
</html>
