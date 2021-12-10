<?php session_start(); ?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Registration</title>
  <link rel="stylesheet" type="text/css" href="style.css" /s>
</head>

<body>

  <!-- display menu option -->
  <div class="topnav">
    <a href="disconnect.php">Back to login screen</a>
  </div>

  <!-- Display message -->
  <div id="menu">
    Please register so you can join in on the fun !
  </div>

  <!-- Registration form -->
  <div id="element1">
    <form action="./registration.php" method="post">
      <table class="center">

        <tr>
          <td>
            <div>
              <label for="email">Email address :<span style="color:red;">*</span> </label>
          </td>
          <td> <input type="email" required id="email" name="adress"> </td>
        </tr>

        <tr>
          <td> Password :<span style="color:red;">*</span> </td>
          <td> <input type="password" required name="pass_registration"> </td>
        </tr>

        <tr>
          <td>
            <div>
              <label>Last name :<span style="color:red;">*</span></label>
          </td>
          <td> <input type="text" required name="lastname"> </td>
        </tr>

        <tr>
          <td>
            <div>
              <label>First name : <span style="color:red;">*</span></label>
          </td>
          <td> <input type="text" required name="firstname"> </td>
        </tr>

        <tr>
          <td>
            <div>
              <label for="phone_number">Phone number : </label>
          </td>
          <td> <input type="tel" id="phone_number" name="phone" minlength="10" step="1"> </td>
        </tr>

        <tr>
          <td>
            <div>
              <label> Role : </label>
          </td>
          <td><select name="role">
              <option value="Reader"> Reader </option>
              <option value="Validator"> Validator </option>
              <option value="Annotator"> Annotator </option>
            </select>
          </td>
        </tr>
        <tr>
          <td colspan=2> <input class="button_ok" type="submit" value="Submit" name="submit_registration"> </td>
        </tr>

      </table>
    </form>
    <br> <span class="small_text"> Fields with a <span style="color:red">*</span> are required.</span>
  </div>


  <!-- Check if all entered informations are valid -->
  <!-- Add to users'list in the database -->

  <?php
  include_once 'libphp/dbutils.php';

  if (isset($_POST['submit_registration'])) {
    connect_db();

    //Retrieve informations
    $values_user = array();
    $values_user['email'] = $_POST["adress"];
    $values_user['pw'] = password_hash($_POST["pass_registration"], PASSWORD_DEFAULT);
    $values_user['last_name'] = $_POST["lastname"];
    $values_user['first_name'] = $_POST["firstname"];
    if (isset($_POST["phone"])) {
      $values_user['phone'] = $_POST["phone"];
    }
    $values_user['role'] = $_POST["role"];
    $values_user['status'] = 'waiting';

    //Query to test if email already in database
    $email_exists = "SELECT u.email FROM database_projet.users u WHERE email = '" . $_POST["adress"] . "';";
    $result_email = pg_query($db_conn, $email_exists);
    if (pg_num_rows($result_email) > 0) {
      echo "<div class=\"alert_bad\">
              <span class=\"closebtn\"
              onclick=\"this.parentElement.style.display='none';\">&times;</span>
              Error during registration. The email " . $_POST["adress"] . " already exists.
            </div>";
    } else {
      $result_insert = pg_insert($db_conn, 'database_projet.users', $values_user);
      if ($result_insert) {
        echo "<div class=\"alert_good\">
            <span class=\"closebtn\"
            onclick=\"this.parentElement.style.display='none';\">&times;</span>
            Registration succeeded, you should have received an email from us.<br>
            Now just wait for validation by an admin.
          </div>";

        // Send email to confirm the registration was succesfull,
        // the user know they put in the right email
        $to = $_POST["adress"]; // Send email to our user
        $subject = "Confirmation of registration"; // Give the email a subject
        $emessage = "Thank you for signing up for our platform.\n The administrator will review your application.\n Expect an update very soon!";

        // if emessage is more than 70 chars
        $emessage = wordwrap($emessage, 70, "\r\n");

        // Our emessage above including the link
        $headers   = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/plain; charset=iso-8859-1";
        $headers[] = "From: Bio Search Sequences <noreply@yourdomain.com>";
        $headers[] = "Subject: {$subject}";
        $headers[] = "X-Mailer: PHP/" . phpversion(); // Set from headers

        mail($to, $subject, $emessage, implode("\r\n", $headers));
      } else {
        echo "<div class=\"alert_bad\">
              <span class=\"closebtn\"
              onclick=\"this.parentElement.style.display='none';\">&times;</span>
              Error during registration.
            </div>";
      }
    }
  }
  ?>

</body>

</html>
