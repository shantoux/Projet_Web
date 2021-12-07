<!-- Web page to display, validate, delete users -->
<?php session_start();
include_once 'libphp/dbutils.php';
connect_db();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users list </title>
    <link rel="stylesheet" type="text/css" href="./style.css" />
  </head>

  <body class="center">

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
      <a href="./user_.php">New search</a>
        <?php
          if ($_SESSION['role'] == 'Annotator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Validator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
          }
          if ($_SESSION['role'] == 'Administrator'){
            echo "<a href=\"./assigned_annotation.php\">Annotate sequence</a>";
            echo "<a href=\"./annotation_validation.php\">Validate annotation</a>";
            echo "<a href=\"./annotation_attribution.php\">Attribute annotation</a>";
            echo "<a href=\"./forum.php\">Forum</a>";
            echo "<a class=\"active\" href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="disconnect.php">Disconnect</a>
        <a class="disc"><?php echo $_SESSION['first_name']?> - <?php echo $_SESSION['role']?> </a>
    </div>

    <h2 id="pagetitle">
      Users' list
    </h2>

    <?php
    if(isset($_POST['submit'])){
      if($_POST['selected_action']=='validate'){
        $values_user = array();
        $values_user['status'] = 'validated';

        $condition = array();
        $condition['email']=$_GET['mail'];

        $result_insert = pg_update($db_conn, 'database_projet.users', $values_user, $condition) or die('Query failed with exception: ' . pg_last_error());
        if ($result_insert){
          echo 'User added to the database';

          //Send email to inform user that their account has been validated
          $to = $_GET['mail']; // Send email to our user
          $subject = "Your account is ready."; // Give the email a subject
          $emessage = "Your account is ready to use !<br>";

          // if emessage is more than 70 chars
          $emessage = wordwrap($emessage, 70, "\r\n");

          // Our emessage above including the link
          $headers   = array();
          $headers[] = "MIME-Version: 1.0";
          $headers[] = "Content-type: text/plain; charset=iso-8859-1";
          $headers[] = "From: The Symposium on Biology and Sequences <noreply@yourdomain.com>";
          $headers[] = "Subject: {$subject}";
          $headers[] = "X-Mailer: PHP/".phpversion(); // Set from headers

          mail($to, $subject, $emessage, implode("\r\n", $headers));

        } else {
          echo 'Error : user has not been added.';
        }
      }

      $query_verif = "SELECT a.annotator FROM database_projet.annotations a WHERE a.annotator = '" .$_GET['mail']. "';";
      $result = pg_query($db_conn, $query_verif) or die('Query failed with exception: ' . pg_last_error());

      if(pg_num_rows($result) == 0){
        if($_POST['selected_action']=='delete'){
          $query_delete = "DELETE FROM database_projet.users WHERE email = '" .$_GET['mail']. "';";
          $result_delete = pg_query($db_conn, $query_delete) or die('Query failed with exception: ' . pg_last_error());
          if ($result_delete){
            echo 'User removed from the database';
          } else {
            echo 'Error';
          }
        }
      } else {
        if($_POST['selected_action']=='delete'){
          echo 'This user can not be removed.';
      }
    }
  }
    ?>

    <div id = "element1">
      <?php
      echo '<table class = "center">';
      echo '<thead>';
      echo '<tr>';
      echo '<th>Last Name</th>';
      echo '<th>First Name</th>';
      echo '<th>User email</th>';
      echo '<th>Role</th>';
      echo '<th>Status</th>';
      echo '<th>Action</th></tr></thead>';

      //Display users waiting to be validated
      echo '<tbody>';
      $query = "SELECT last_name, first_name, email, role, status
      FROM database_projet.users WHERE status='waiting' ORDER BY role;";
      $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

      if(pg_num_rows($result) > 0){
        for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++){
          $last_name = pg_fetch_result($result, $res_nb, 0);
          $first_name = pg_fetch_result($result, $res_nb, 1);
          $email = pg_fetch_result($result, $res_nb, 2);
          $role = pg_fetch_result($result, $res_nb, 3);
          $status = pg_fetch_result($result, $res_nb, 4);

          echo '<tr><td>';
          echo $last_name;
          echo '</td><td>';
          echo $first_name;
          echo '</td><td>';
          echo $email;
          echo '</td><td>';
          echo $role;
          echo '</td><td><b>';
          echo $status;
          echo '</b></td><td>';
          echo '<form action="./user_list.php?mail=' . $email . '"method="post"><select name="selected_action">';
          echo '<option value="validate">Validate</option>';
          echo '<option value="delete">Delete</option>';
          echo '</select><input type="submit" value="submit" name="submit">';
          echo '</td></form></tr>';
        }
      }
      echo '</tbody><br>';

      //Display users already in database
      echo '<tbody>';
      $query = "SELECT last_name, first_name, email, role, status
      FROM database_projet.users WHERE status='validated' ORDER BY role;";
      $result = pg_query($db_conn, $query) or die('Query failed with exception: ' . pg_last_error());

      if(pg_num_rows($result) > 0){
        for ($res_nb = 0; $res_nb < pg_num_rows($result); $res_nb++){
          $last_name = pg_fetch_result($result, $res_nb, 0);
          $first_name = pg_fetch_result($result, $res_nb, 1);
          $email = pg_fetch_result($result, $res_nb, 2);
          $role = pg_fetch_result($result, $res_nb, 3);
          $status = pg_fetch_result($result, $res_nb, 4);

          echo '<tr><td>';
          echo $last_name;
          echo '</td><td>';
          echo $first_name;
          echo '</td><td>';
          echo $email;
          echo '</td><td>';
          echo $role;
          echo '</td><td>';
          echo $status;
          echo '</td><td>';
          echo '<form action="./user_list.php?mail=' . $email . '"method="post"><select name="selected_action">';
          echo '<option value="delete">Delete</option>';
          echo '</select>';
          echo '</select><input type="submit" value="submit" name="submit">';
          echo '</td></form></tr>';
        }
      }
      echo '</tbody>';

      echo '</table>';
          ?>
    </div>
  </body>
</html>
