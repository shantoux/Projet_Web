<html>
<head> <meta charset="UTF-8">
<title>Registration form</title>
<link rel="stylesheet" type="text/css" href="./pw_style.css">
</head>

<body>
  <div id="menu">
    Please register so you can join in on the fun !
  </div>

<form>
  <table class="center">

    <tr>
      <td> Email address : </td>
      <td> <input type="text" name="adress"> </td>
    </tr>

    <tr>
      <td> Last name : </td>
      <td> <input type="text" name="lastname"> </td>
    </tr>

    <tr>
      <td> First name : </td>
      <td> <input type="text" name="firstname"> </td>
    </tr>

    <tr>
      <td> Phone number : </td>
      <td> <input type="text" name="phone"> </td>
    </tr>

    <tr>
      <td> Role : </td>
      <td><select name="role">
        <option value="Reader"> Reader </option>
        <option value="validator"> Validator </option>
        <option value="Annotator"> Annotator </option>
      </select>
    </td>
    </tr>


  </table>
</form>
</html>
