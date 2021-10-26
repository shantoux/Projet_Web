<!-- Web page to make a search, user is automatically brought here after loging in -->

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navigation </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body class="center">
    <?php
      # TODO: un-hardcode the user role, check in database for the actual role
      $role = "administrator";
      $roles = array("annotator", "validator", "administrator");
      $url_array = preg_split("#/#", $_SERVER['HTTP_REFERER']);
    ?>

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <a class="active" href="./search_1.php">New search</a>
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
    
    <!-- Display info box for successful login -->
    <?php
      if (end($url_array) == "Login_page1.php") {
        echo "<br> <div class=\"alert_good\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Authentification successful :)
        </div>";
      }
    ?>

    <h2> Search our database </h2>
    <br> Search for a gene / proteine sequence, or for a whole genome.

    <br> <br> <br>
    <div id="element1">
      <form action="database_search_result.php" method = "post">
        <table class="center">
          <tr>
            <td> Nucleotides sequence: </td>
            <td> <input type="text" name="nucl_sequence"> </td>
          </tr>
          <tr>
            <td> Peptides sequence: </td>
            <td> <input type="text" name="pep_sequence"> </td>
          </tr>
          <tr>
            <td> Genes names: </td>
            <td> <input type="text" name="genes"> </td>
          </tr>
          <tr>
            <td> Transcripts names: </td>
            <td> <input type="text" name="transcripts"> </td>
          </tr>
          <tr>
            <td> Description: </td>
            <td> <textarea cols="29" rows="5" name="description"></textarea> </td>
          </tr>
          <tr>
            <td> Type of search:<span style="color:red;">*</span></td>
            <td align="left"> <input type="radio" id="genome" name="search_type" required> <label for="genome">Genome</label> </td>
          <tr>
            <td></td>
            <td align="left"> <input type="radio" id="gene_prot" name="search_type" required> <label for="gene_prot">Gene / Proteine</label> </td>

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
