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
            <td colspan=2> <input type ="submit" value="Log in" name = "submit"> </td>
          </tr>
        </table>

      </form>
    </div>

<!-- Le popup ci-dessous doit ensuite intégrer la connexion à la DB pour checker l'Utilisateur -->

    <?php
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
    ?>

<?php
//essai connexion postgres
$db = pg_connect("host=localhost port=5432 dbname=shanti_psql username = shanti") or die ("Connection échoué");
//syntaxe connexion conditionnelle :
$essai_name = "username";
$essai_password  = "password";
if(isset($_POST['submit'])){
  if ($_POST['name'] == $essai_name && $_POST['pass']== $essai_password){
    echo '<script>location.href="search_1.php"</script>';
  }
  else{
    echo "Utilisateur ou mot de passe erronés";
  }
}
?>
  </body>
</html>
