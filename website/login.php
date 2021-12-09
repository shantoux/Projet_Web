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

  <!-- Vérification de l'email, du mot de passe et du statut validé ou non de l'utilisateur pour accéder à la search page -->
  <!-- -->
  <?php
  include_once 'libphp/dbutils.php';
  connect_db();
  if (isset($_POST['submit'])){
    $user_name = $_POST["name"];
    $user_password = $_POST["pass"];

    $query = "SELECT * FROM database_projet.users u WHERE u.email = '$user_name';"; //AND u.pw = '$user_password';";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    $hash = pg_fetch_result($result, 0, 1);

    if (password_verify($user_password, $hash)){
      $validated = pg_fetch_result($result, 0, 6) == 'validated'; //get the result of the 7th column (Status) for the 1st row
      if ($validated){
        echo '<script>location.href="search.php"</script>';

        // Start a session and store variables email and role
        session_start();
        $_SESSION['user'] = $_POST['name'];
        $_SESSION['role'] = pg_fetch_result($result, 0, 5);
        $_SESSION['first_name'] = pg_fetch_result($result, 0, 3);
        $_SESSION['last_name'] = pg_fetch_result($result, 0, 2);
      } else {
        echo "<div class=\"alert_bad\">
        <span class=\"closebtn\"
        onclick=\"this.parentElement.style.display='none';\">&times;</span>
        Your account has not been validated by an admin yet.
        </div>";
      }
    } else { //si t'es dans la base
      $query_base = "SELECT * FROM database_projet.users WHERE email = '$user_name' AND pw = '$user_password';";
      $result_base = pg_query($db_conn, $query_base) or die('Query failed with exception: ' . pg_last_error());

      if (pg_num_rows($result_base) == 1){
        $validated = pg_fetch_result($result, 0, 6) == 'validated';
        if ($validated){
          echo '<script>location.href="search.php"</script>';
          // Start a session and store variables email and role
          session_start();
          $_SESSION['user'] = $_POST['name'];
          $_SESSION['role'] = pg_fetch_result($result, 0, 5);
          $_SESSION['first_name'] = pg_fetch_result($result, 0, 3);
          $_SESSION['last_name'] = pg_fetch_result($result, 0, 2);
        } else {
          // If there's no result to the query : wrong pair of email/pw
          echo "<div class=\"alert_bad\">
          <span class=\"closebtn\"
          onclick=\"this.parentElement.style.display='none';\">&times;</span>
          Wrong Username or Password.</div>";
        }
      }
    }
  }
  ?>

  <!-- Login form -->
  <div id="element1">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
</body>
</html>
