<!-- Web page to login or access the registration page -->

<!DOCTYPE html>
<html>

  <!-- Page header -->
  <head>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1">
    <title>Website_title</title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>
  <body class="center">

    <!-- Page header -->
    <h1> Welcome to Bio Search Sequences </h1>
    <div id="menu">
      Please log in and let's annotate!<br>
    </div>

    <!-- Login form -->
    <div id="element1">
      <form action="<?php echo $_SERVER['PHP_SELF'];?>" method = "post">
        <table class="center">
          <tr>
            <td> Login : </td>
            <!-- #TODO: unhardcode login and pw -->
            <td> <input type="text" name="name" value="bobby@gmail.com"> </td>
          </tr>
          <tr>
            <td> Password : </td>
            <td> <input type="password" name="pass" value="cestmoibobby"> </td>
          </tr>
          <tr>
            <td colspan=2> <input type="submit" value="Log in" name="submit" onsubmit="myButton.disabled=true; return true;"> </td>
          </tr>
        </table>
      </form>

      <br> <br> <span class="small_text"> Not already registered? <a href="./registration.php">Click here</a> to submit a new account.</span>
    </div>

    <!-- Vérification de l'email, du mot de passe et du statut validé ou non de l'utilisateur pour accéder à la search page -->
    <!-- -->
    <?php
    include_once 'libphp/dbutils.php';

    if(isset($_POST['submit'])){
      connect_db();

      //Get email and password filled in the connexion form
      $user_name = $_POST["name"];
      $user_password = $_POST["pass"];

      // Query : Select all user info for a specified email and password
      $query = "SELECT * FROM database_projet.users WHERE email = '$user_name' AND pw = '$user_password';";
      $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());


    	if(pg_num_rows($result) == 1){
        //If there's only one result to the query
        $validated= pg_fetch_result($result,0, 6) == 'validated'; //get the result of the 7th column (Status) for the 1st row
        if($validated){
          echo '<script>location.href="search.php"</script>';

          session_start();
          $_SESSION['user'] = $_POST['name'];
          $_SESSION['role'] = pg_fetch_result($result, 0, 5); //récupère le résultat de la 6e colonne (5) première ligne (0)
        }
        else{
          echo "<div class=\"alert_bad\">
            <span class=\"closebtn\"
            onclick=\"this.parentElement.style.display='none';\">&times;</span>
            Your account has not been validated by an admin yet.
          </div>";
          }

      }
      else{
        echo "<div class=\"alert_bad\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Wrong Username or Password.
        </div>";
        }
  }
?>

  </body>
</html>
