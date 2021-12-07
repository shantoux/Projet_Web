<?php session_start();
session_destroy(); // destroys all the data associated with the current session
echo '<script>location.href="login.php"</script>'; // return to login page
?>
