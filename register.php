<?php
// db connectie
require 'includes/db_connect.php';

// error variabele maken
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']); // naam ophalen en hack-proof maken met "trim" (op internet opgezicht)
    $password = trim($_POST['password']); // wachtwoord pakken en ook hack-proofen

    if (empty($username) || empty($password)) {
        $error = 'All fields are required.'; // error als beide leeg zijn
    } else {
        // voorkomen dat er 2x dezelfde naam komen
        $stmt = $users_db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = 'Username already taken.'; // eroor als dit het geval is
        } else {
            // wachtwoord hashen (ook van het internet)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // niewe accounts toevoegen
            $stmt = $users_db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);

            // terug naar inlogpagina
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css"> <!-- externe css -->
</head>
<body>
    <div class="form-container">
        <h1>Register</h1>
        <?php if ($error): ?> <!-- error bericht laten zien -->
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required> <!-- naam verplichten -->
            <input type="password" name="password" placeholder="Password" required> <!-- wachtwoord input -->
            <button type="submit">Register</button> <!-- "submit" button -->
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p> <!-- inloglink -->
    </div>
</body>
</html>
