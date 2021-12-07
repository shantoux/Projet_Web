<!-- Web page to make a search, user is automatically brought here after loging in -->
<?php session_start();?>

<!DOCTYPE html>
<html>
  <!-- LOL COUCOU -->

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navigation </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">
    <?php
      $url_array = preg_split("#/#", $_SERVER['HTTP_REFERER']);
    ?>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a class="active" href="./search.php">New search</a>
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
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a href=\"./user_list.php\">Users list</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc"> Hello <?php echo $_SESSION['first_name']?></a>
        <a class="disc" href="disconnect.php">Disconnect</a>
    </div>

    <!-- Display info box for successful login -->
    <?php
      if (end($url_array) == "login.php") {
        echo "<br> <div class=\"alert_good\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Authentification successful :)
        </div>";
      }
    ?>

    <h2 id="pagetitle"> Search our database </h2>
    Search for a gene / proteine sequence, or for a whole genome.

    <br> <br> <br>
    <div id="element1">
      <form action="search_result.php" method = "post">
        <table class="center">
        <tr>
            <td> Type of search:<span style="color:red;">*</span></td>
            <td align="left"> <input type="radio" id="genome" name="search_type" value="genome" required> <label for="genome">Genome</label> </td>
          <tr>
            <td></td>
            <td align="left"> <input type="radio" id="gene_prot" name="search_type" value="gene_prot" required> <label for="gene_prot">Gene / Proteine</label> </td>

          </tr>
          <tr>
            <td> Specie / Name / Strain: </td>
            <td> <input type="text" name="specie" size="100"> </td>
          </tr>
          <tr>
            <td> Sequence identifier: </td>
            <td> <input type="text" name="seq_id" size="100"> </td>
          </tr>
          <tr>
            <td> Nucleotides sequence: </td>
            <td> <input type="text" name="nucl_sequence" minlength="3" maxlength="916" size="100"> </td>
          </tr>
          <tr>
            <td> Peptides sequence: </td>
            <td> <input type="text" name="pep_sequence" minlength="3" maxlength="304" size="100"> </td>
          </tr>
          <tr>
            <td> Genes names: </td>
            <td> <input type="text" name="genes" size="100"> </td>
          </tr>
          <tr>
            <td> Description: </td>
            <td> <textarea cols="80" rows="5" name="description"></textarea> </td>
          </tr>
          <tr>
            <td colspan=2> <br> <br> <input type ="submit" value="Search!" name = "submit"> </td>
          </tr>

        </table>
        <br> <span class="small_text"> Fields with a <span style="color:red">*</span> are required.</span>

      </form>

    </div>

  </body>
</html>
