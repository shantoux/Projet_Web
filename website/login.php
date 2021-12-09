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
  connect_db(); // connexion to database

  // check if connection button has been clicked
  if (isset($_POST['submit'])){

    //Get email and password filled in the connexion form
    $user_name = $_POST["name"];
    $user_password = $_POST["pass"];

    // retrieve the password for this user in the database
    $query = "SELECT u.pw, u.status, u.role, u.first_name, u.last_name FROM database_projet.users u WHERE u.email = '" . $user_name . "';";
    $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

    // check if an account exist with this email
    if (pg_num_rows($result) > 0) {

      // build hashed pw
      $hash = pg_fetch_result($result, 0, 0);

      // check if user is validated by admin
      $validated = pg_fetch_result($result, 0, 1) == 'validated';

      // check if hashed pw matches
      if ((password_verify($user_password, $hash) || $user_password == pg_fetch_result($result, 0, 0)) && $validated) {

        // Go to the search page
        echo '<script>location.href="search.php"</script>';

        // If the user's status is "validated" (approved by the site's admin)
        // Start a session and store variables email and role
        session_start();
        $_SESSION['user'] = $_POST['name'];
        $_SESSION['role'] = pg_fetch_result($result, 0, 2);
        $_SESSION['first_name'] = pg_fetch_result($result, 0, 3);
        $_SESSION['last_name'] = pg_fetch_result($result, 0, 4);

        // update last login
        $login_time = array();
        $login_time['last_login'] = 'now()';

        $condition = array();
        $condition['email'] = $_POST['name'];

        $update = pg_update($db_conn, 'database_projet.users', $login_time, $condition) or die('Query failed with exception: ' . pg_last_error());

      }

      else if (!$validated) {

        // display error message for un-validated account
        echo "<div class=\"alert_bad\">
        <span class=\"closebtn\"
        onclick=\"this.parentElement.style.display='none';\">&times;</span>
        Your account has not been validated by an admin yet.
        </div>";
      }

      else {

        // If pw do not match
        echo "<div class=\"alert_bad\">
        <span class=\"closebtn\"
        onclick=\"this.parentElement.style.display='none';\">&times;</span>
        Wrong Username or Password.</div>";

      }
    }

    // if no account have been found
    else {
      echo "<div class=\"alert_bad\">
      <span class=\"closebtn\"
      onclick=\"this.parentElement.style.display='none';\">&times;</span>
      Wrong Username or Password.</div>";
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
