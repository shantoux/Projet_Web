<html>

  <head>
    <meta charset="UTF-8">
    <title>Website_title</title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css">
  </head>

  <body>
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


  

<?php
//essai connexion postgres
$db = pg_connect("host=localhost port=5432 dbname=shanti_psql username = shanti") or die ("Connection échoué");
//syntaxe connexion conditionnelle :
$essai_name = "username";
$essai_password  = "password";
if(isset($_POST['submit'])){
  if ($_POST['name'] == $essai_name && $_POST['pass']== $essai_password){
    echo '<meta http-equiv="refresh" content="0;url=site_2.php" />';
  }
  else{
    echo "Utilisateur ou mot de passe erronés";
  }
}
?>
  </body>
</html>
