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

<!-- TODO: Le popup ci-dessous doit ensuite intégrer la connexion à la DB pour checker l'Utilisateur -->

<!--     <?php
    $essai_name = "username";
    $essai_password  = "password";
    if(isset($_POST['submit'])){
      if ($_POST['name'] == $essai_name && $_POST['pass']== $essai_password){
        echo '<script>location.href="search_1.php"</script>';
      }
      else{
        echo "<div class=\"alert_bad\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Wrong username or password.
        </div>";
      }
    }
    ?> -->

    <?php
    //essai connexion postgres
    $db_conn = pg_connect("host=tp-postgres user=spijeau_a password=spijeau_a");

    if(isset($_POST['submit'])){
      //Récupération du nom et password rempli dans le formulaire de connexion
      $user_name = $_POST["name"];
      $user_password = $_POST["pass"];

      // Ex�cution de la requ�te SQL
      $query = "SELECT * FROM annotation_seq.users WHERE email = '$user_name' AND pw = '$user_password';";
      $result = pg_query($db_conn, $query);
      if(pg_num_rows($result) != 1){
        echo "<div class=\"alert_bad\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Wrong username or password.
        </div>";
      }
      else{
        echo '<script>location.href="search_1.php"</script>';
      }
    }
?>
  </body>
</html>
