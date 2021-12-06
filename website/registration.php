<?php session_start();?>

<!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <title>Registration form</title>
    <link rel="stylesheet" type="text/css" href="style.css" /s>
  </head>

<body>
  <!-- display menu option -->
  <div class="topnav">
        <a href="login.php">Back to login screen</a>
    </div>

  <div id="menu">
    Please register so you can join in on the fun !
  </div>

<div id="element1">
<form action="./registration.php" method = "post">
  <table class="center">

    <tr>
      <td> <div>
        <label for ="email">Email address :<span style="color:red;">*</span> </label></td>
      <td> <input type="email" required id="email" name="adress"> </td>
    </tr>

    <tr>
      <td> Password :<span style="color:red;">*</span> </td>
      <td> <input type="password" required name="pass_registration"> </td>
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
      <td> <input type="tel" id="phone_number" name="phone" minlength="10" step="1"> </td>
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
<br> <span class="small_text"> Fields with a <span style="color:red">*</span> are required.</span>
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
            Registration succeeded, wait for validation by an admin.
          </div>";
          $to = $_POST["adress"]; // Send email to our user
          $subject = "Confirmation of registration"; // Give the email a subject
          $emessage = "Thank you for wanting to use our website. <br>
          The administrator will review your application very soon.";

          // if emessage is more than 70 chars
          $emessage = wordwrap($emessage, 70, "\r\n");

          // Our emessage above including the link
          $headers   = array();
          $headers[] = "MIME-Version: 1.0";
          $headers[] = "Content-type: text/plain; charset=iso-8859-1";
          $headers[] = "From: no-reply <noreply@yourdomain.com>";
          $headers[] = "Subject: {$subject}";
          $headers[] = "X-Mailer: PHP/".phpversion(); // Set from headers

          mail($to, $subject, $emessage, implode("\r\n", $headers));
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
