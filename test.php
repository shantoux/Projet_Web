<?php
  //Selectionner la bonne connexion
  //--------------------------------
//phpinfo();
  $db = pg_connect("host=localhost port=5432 dbname=public username = soundous") or die ("Connection échoué");


?>
