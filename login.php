<?php
require 'includes/db_connect.php';
session_start(); // sessie om user te tracken (deze ga je nog een hoop terugzien)

// error variabele maken want anders doet ie het niet
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //post method zoals in de les uitgelegd
    $username = trim($_POST['username']); // "trim" komt van het internet, die staat ook in een paar andere bestadnen
    $password = trim($_POST['password']); // 

    // user uit de database halen
    $stmt = $users_db->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(); // user pakken

    // wachtwoord veriefieren en sessie beginnen
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; // id in sessie vermelden
        $_SESSION['username'] = $username; // naam in sessie vermelden

        // terug naar het menu voor taken enz
        header('Location: tasks.php');
        exit;
    } else {
        $error = 'Invalid username or password.'; // error als het niet klopt
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css"> <!-- externe css zoals vorig jaar behandelt (komt nog een hoop terug) -->
</head>
<body>
    <div class="form-container">
        <h1>Login</h1>
        <?php if ($error): ?> <!-- eroor bericht laten zien -->
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required> <!-- username invullen -->
            <input type="password" name="password" placeholder="Password" required> <!-- wachtwoord invullen -->
            <button type="submit">Login</button> <!-- submit knop -->
        </form>
        <p>Don't have an account? <a href="register.php">Register</a></p> <!-- link naar account maken -->
    </div>
</body>
</html>
