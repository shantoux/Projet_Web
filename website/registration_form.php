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
<form>
  <table class="center">

    <tr>
      <td> <div>
        <label>Email address : </label></td>
      <td> <input type="text" name="adress"> </td>
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
      <td colspan=2> <input type ="submit" value="Submit" name = "submit"> </td>
    </tr>

  </table>
</form>
</div>
</body>
</html>
