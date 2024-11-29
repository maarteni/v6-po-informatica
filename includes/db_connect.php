<?php
/**
 * code omdat ik 2 rdbms nodig had
 */

try {
    // Connectie naar rdbms 1 (deze wordt voor users)
    $users_db = new PDO("mysql:host=localhost;dbname=users_db;charset=utf8", 'root', '');
    $users_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // errors mogelijk maken (snap ik niet helemaal maar stackoverflow zij dat het ged was)
    $users_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // deze is van het internet
	
    // connectie voor rdbms 2 (voor taken)
    $tasks_db = new PDO("mysql:host=localhost;dbname=tasks_db;charset=utf8", 'root', '');
    $tasks_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // errors mogelijk maken (hetzelfde als hiervoor)
    $tasks_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // internet code

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage()); // bericht wat ik later heb toegevoegd omdat het niet werkte
}
?>
