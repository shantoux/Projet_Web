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
<form action="./Login_page1.php" method = "post">
  <table class="center">

    <tr>
      <td> <div>
        <label>Email address : </label></td>
      <td> <input type="text" name="adress"> </td>
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
        <label>Phone number : </label></td>
      <td> <input type="text" name="phone"> </td>
    </tr>

    <tr>
      <td> <div>
        <label>Role : </label></td>
      <td><select name="role">
        <option value="Reader"> Reader </option>
        <option value="validator"> Validator </option>
        <option value="Annotator"> Annotator </option>
      </select>
    </td>
    </tr>
    <tr>
      <td colspan=2> <input type ="submit" value="Submit" name = "submit_registration" onsubmit="myButton.disabled = true; return true;"onsubmit="myButton.disabled = true; return true;"> </td>
    </tr>

  </table>
</form>

<!-- Add to users'list in the database -->
<?php
if(isset($_POST['submit_registration'])){
  connect_db();
  //Retrieve informations
  $first_name = $_POST["firstname"];
  $last_name = $_POST["lastname"];
  $email_registration = $_POST["adress"];
  $phone_number = $_POST["phone"];
  $password_registration = $_POST["pass_registration"];
  $role_registration = $_POST["role"];

  //Query in postgres
  $add_user = "INSERT INTO users (email, pw, last_name, first_name, phone, role, status) 
  VALUES ($email_registration, $password_registration, $last_name, $first_name, $phone_number,$role_registration, 'waiting');";
  $result_registration = pg_query($db_conn, $add_user) or die('Query failed with exception: ' . pg_last_error());
}
?>

</div>
</body>
</html>
