<!-- Web page to make a search, user is automatically brought here after logging in -->
<?php session_start();

  // check if user is logged in: else, redirect to login page
  if (!isset($_SESSION['user'])) {
    echo '<script>location.href="login.php"</script>';
  }

?>

<!DOCTYPE html>
<html>

  <!-- Page header -->
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navigation </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">

    <!-- Retrieve url of previous page to later display the "successful log in event" if needed -->
    <?php
      $url_array = preg_split("#/#", $_SERVER['HTTP_REFERER']);
    ?>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a class="active" href="./search.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'Annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./consult_annotation.php\">Consult</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users list</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
        <a class="disc"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
    </div>

    <!-- Display info box for successful login -->
    <?php
      if (end($url_array) == "login.php") {
        echo "<br> <div class=\"alert_good\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Hello " . $_SESSION['first_name'] ."! Good to see you :)
        </div>";
      }
    ?>

    <!-- Display fancy box -->
    <div class="fancy_box">

      <!-- Display page title -->
      <div id="pagetitle"> Search our database </div>
      Search for a gene / proteine sequence, or for a whole genome.

      <!-- Display search form -->
      <br> <br> <br>
      <div id="element1">
        <form action="search_result.php" method = "post">
          <table class="center">

            <!-- Add required choice between a search on genomes or on sequences -->
            <tr>
              <td> Type of search:<span style="color:red;">*</span></td>
              <td align="left"> <input type="radio" id="genome" name="search_type" value="genome" required> <label for="genome">Genome</label> </td>
            <tr>
              <td></td>
              <td align="left"> <input type="radio" id="gene_prot" name="search_type" value="gene_prot" required> <label for="gene_prot">Gene / Proteine</label> </td>
            </tr>

            <!-- Add a field for name of specie / strain -->
            <tr>
              <td> Specie / Name / Strain: </td>
              <td> <input type="text" name="specie" size="100"> </td>
            </tr>

            <!-- Add a field for sequence id -->
            <tr>
              <td> Sequence identifier: </td>
              <td> <input type="text" name="seq_id" size="100"> </td>
            </tr>

            <!-- Add a field for nucleotidic sequence -->
            <tr>
              <td> Nucleotide sequence: </td>
              <td> <input type="text" name="nucl_sequence" minlength="3" maxlength="916" size="100"> </td>
            </tr>

            <!-- Add a field for peptidic sequence -->
            <tr>
              <td> Peptide sequence: </td>
              <td> <input type="text" name="pep_sequence" minlength="3" maxlength="304" size="100"> </td>
            </tr>

            <!-- Add a field for gene id -->
            <tr>
              <td> Gene identifier: </td>
              <td> <input type="text" name="gene_id" size="100"> </td>
            </tr>

            <!-- Add a field for gene biotype -->
            <tr>
              <td> Gene biotype: </td>
              <td> <input type="text" name="gene_biotype" size="100"> </td>
            </tr>

            <!-- Add a field for transcript biotype -->
            <tr>
              <td> Transcript biotype: </td>
              <td> <input type="text" name="transcript_biotype" size="100"> </td>
            </tr>

            <!-- Add a field for gene symbol -->
            <tr>
              <td> Gene name: </td>
              <td> <input type="text" name="gene_symb" size="100"> </td>
            </tr>

            <!-- Add a field for gene identifier -->
            <tr>
              <td> Description: </td>
              <td> <textarea cols="92" rows="5" name="description"></textarea> </td>
            </tr>

            <!-- Display search buttton -->
            <tr>
              <td colspan=2> <br> <br> <input type ="submit" value="Search!" name = "submit"> </td>
            </tr>

          </table>

          <!-- add text for required fields -->
          <br> <span class="small_text"> Fields with a <span style="color:red">*</span> are required.</span>

        </form>

      </div>
    </div>

  </body>
</html>
