<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <title>Registration form</title>
    <link rel="stylesheet" type="text/css" href="pw_style.css" /s>
  </head>

<body>
  <!-- display menu option -->
  <div class="topnav">
        <a href="Login_page1.php">Back to login screen</a>
    </div>

  <div id="menu">
    Please register so you can join in on the fun !
  </div>

<div id="element1">
<form action="./registration_form.php" method = "post">
  <table class="center">

    <tr>
      <td> <div>
        <label for ="email">Email address : </label></td>
      <td> <input type="email" id="email" name="adress"> </td>
    </tr>

    <tr>
      <td> Password : </td>
      <td> <input type="password" name="pass_registration"> </td>
    </tr>

    <tr>
      <td> <div>
        <label>Last name : </label></td>
      <td> <input type="text" name="lastname"> </td>
    </tr>

    <tr>
      <td> <div>
        <label>First name : </label></td>
      <td> <input type="text" name="firstname"> </td>
    </tr>

    <tr>
      <td> <div>
        <label for = "phone_number">Phone number : </label></td>
      <td> <input type="number" id="phone_number" name="phone" minlength="10" step="1"> </td>
    </tr>

    <tr>
      <td> <div>
        <label>Role : </label></td>
      <td><select name="role">
        <option value="reader"> Reader </option>
        <option value="validator"> Validator </option>
        <option value="annotator"> Annotator </option>
      </select>
    </td>
    </tr>
    <tr>
      <td colspan=2> <input type ="submit" value="Submit" name = "submit_registration"> </td>
    </tr>

  </table>
</form>
</div>


<!-- Check if all entered informations are valid -->
<!-- Add to users'list in the database -->

<?php
include_once 'libphp/dbutils.php';

if(isset($_POST['submit_registration'])){
  connect_db();

  //Retrieve informations
  $values_user = array();
  $values_user['email'] = $_POST["adress"];
  $values_user['pw'] = $_POST["pass_registration"];
  $values_user['last_name'] = $_POST["lastname"];
  $values_user['first_name'] = $_POST["firstname"];
  $values_user['phone'] = $_POST["phone"];
  $values_user['role'] = $_POST["role"];
  $values_user['status'] = 'waiting';

  $result_insert = pg_insert($db_conn, 'annotation_seq.users', $values_user);
  if ($result_insert) {
    echo "<div class=\"alert_good\">
            <span class=\"closebtn\"
            onclick=\"this.parentElement.style.display='none';\">&times;</span>
            Registration Succeeded.
          </div>";
  } else {
    echo "<div class=\"alert_bad\">
              <span class=\"closebtn\"
              onclick=\"this.parentElement.style.display='none';\">&times;</span>
              Error during registration.
            </div>";
}
}
?>


</body>
</html>
