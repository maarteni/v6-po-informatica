<?php
session_start(); // sessie starten ivm account
session_destroy(); // sessie "destroyen" om uit te loggen
header('Location: login.php'); // terug naar inlog pagina
exit;
?>
