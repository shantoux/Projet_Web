<html>

  <head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Website_title</title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>
  <body class="center">

    <h1> Welcome to the Symposium on Biology and Sequences </h1>
    <div id="menu">
      Welcome to our brand new website login page!<br>
      Everything will be up and runing soon.
    </div>

    <div id="element1">
      <form action="<?php echo $_SERVER['PHP_SELF'];?>" method = "post">
        <table class="center">
          <tr>
            <td> Login : </td>
            <td> <input type="text" name="name"> </td>
          </tr>
          <tr>
            <td> Password : </td>
            <td> <input type="password" name="pass"> </td>
          </tr>
          <tr>
            <td colspan=2> <input type="submit" value="Log in" name="submit" onsubmit="myButton.disabled=true; return true;"> </td>
          </tr>
        </table>

      </form>

      <br> <br> <span class="small_text">Not already registered? <a href="./registration_form.php">Click here</a> to submit a new account.</span>
    </div>
    <?php
      // Connexion à la base de donn�es
      $db_conn = pg_connect("host=tp-postgres user=sbellab_a password=sbellab_a");

      if(isset($_POST['submit'])){
        // R�cup�ration du nom pass� en param�tre (pour recherche)
        $user_email = $_POST['name'];
        $user_pw = $_POST['pass'];

        $query = "SELECT email, pw FROM annotation_seq.users
              WHERE email = '$user_email' AND pw = '$user_pw';";

        $result = pg_query($db_conn, $query)
              or die('Query failed with exception: ' . pg_last_error());

        if(pg_num_rows($result) == 1){
          echo '<script>location.href="search_1.php"</script>';
          }
          else{
            echo "<div class=\"alert_bad\">
              <span class=\"closebtn\"
              onclick=\"this.parentElement.style.display='none';\">&times;</span>
              Wrong username or password.
            </div>";
          }
          // Lib�re le r�sultat
          //pg_free_result($result);

          // Ferme la connexion
          //pg_close($db_conn);
        }
      ?>
  </body>
</html>
