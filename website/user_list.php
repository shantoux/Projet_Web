<!-- Web page to display, validate, delete users -->
<?php session_start();
include_once 'libphp/dbutils.php';
connect_db();?>

<!DOCTYPE html>
<html>

  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users list </title>
    <link rel="stylesheet" type="text/css" href="./pw_style.css" />
  </head>

  <body class="center">

    <!-- display menu options depending of the user's role -->
    <div class="topnav">
        <?php
          if ($_SESSION['status'] == 'annotator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
          }
          if ($_SESSION['status'] == 'validator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
          }
          if ($_SESSION['status'] == 'administrator'){
            echo "<a href=\"./annotation_1.php\">Annotate sequence</a>";
            echo "<a href=\"./validation_1.php\">Validate annotation</a>";
            echo "<a href=\"./seq_attribution_1.php\">Attribute annotation</a>";
            echo "<a href=\"./user_list.php\">Users' List</a>";
          }
        ?>
        <a href="about.php">About</a>
        <a class="disc" href="Login_page1.php">Disconnect</a>
    </div>

    <h2 id="pagetitle">
      Users' list
    </h2>

    <?php
    if(isset($_POST['submit'])){
      if(isset($_POST['validate'])){
        echo "that's something";
        $values_user = array();
        $values_user['status'] = 'validated';

        $condition = array();
        $condition['email']=$_POST['submit'];

        $result_insert = pg_update($db_conn, 'annotation_seq.users', $values_user, $condition) or die('Query failed with exception: ' . pg_last_error());
        if ($result_insert){
          echo 'User added to the database';
        } else {
          echo 'Error : user has not been added.';
        }
      }
      if(isset($_POST['delete'])){
        $query_delete = "DELETE FROM annotation_seq.users WHERE email = \'" .$_POST['submit']. "';";
        $result_delete = pg_query($db_conn, $query_delete) or die('Query failed with exception: ' . pg_last_error());
        if ($result_delete){
          echo 'User removed from the database';
        } else {
          echo 'Error';
        }
      }
    }?>

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
      FROM annotation_seq.users WHERE status='waiting' ORDER BY last_name;";
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
          echo "<form action=\"./user_list.php\" method=\"post\">";
          echo '<select name="action">';
          echo '<option value="validate" name="validate">Validate</option>';
          echo '<option value="Delete" name="delete">Delete</option>';
          echo '</select>';
          echo '<button type="submit" value='.$email.'name="submit">submit</button>';
          echo '</td></form></tr>';
        }
      }
      echo '</tbody><br>';

      //Display users already in database
      echo '<tbody>';
      $query = "SELECT last_name, first_name, email, role, status
      FROM annotation_seq.users WHERE status='validated' ORDER BY last_name;";
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
          echo "<form action=\"./user_list.php\" method=\"post\">";
          echo '<select name="action">';
          echo '<option value="Delete" name="delete">Delete</option>';
          echo '</select>';
          echo '<button type="submit" value='.$email.'name="submit">submit</button>';
          echo '</td></form></tr>';
        }
      }
      echo '</tbody>';

      echo '</table>';
          ?>

    </div>

    </form>
  </body>
</html>
