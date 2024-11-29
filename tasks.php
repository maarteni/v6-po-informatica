<?php
session_start(); // sessie beginnen om user te tracken
require 'includes/db_connect.php'; // voorkomen dat je fatal error krijgt

// inloggen checken
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // terug naar inlogpagina voor als je niet oplet
    exit;
}

// id opvragen
$user_id = $_SESSION['user_id'];

// variabelen beginnen (allemaal op 1 plek omdat ik het anders niet meer terugvind in dit zootje)
$error = '';
$success = '';
$task_to_edit = null; // To handle task editing mode

// taak maken
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_task'])) {
    $title = trim($_POST['title']); // taak titel pakken en sql injectie voorkomen ("trim"heb ik van het internet)
    $description = trim($_POST['description']); // 
    $due_date = $_POST['due_date'] ?? null; // inleverdatum niet verplicht

    if (empty($title)) {
        $error = 'Task title is required.'; // error code voor als iemand de titel verkloot
    } else {
        // nieuwe taak in database doen
        $stmt = $tasks_db->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)"); //insert into komt ook van het internet
        $stmt->execute([$user_id, $title, $description, $due_date]);
        $success = 'Task created successfully!';
    }
}

// taak weghalen
if (isset($_GET['delete'])) {
    $task_id = (int)$_GET['delete']; // taak id ophalen
    $stmt = $tasks_db->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
    $success = 'Task deleted successfully!'; //zodat je een goed gevoel krijgt wanneer je dingen werken
}

// taken bewerken 
if (isset($_GET['edit'])) {
    $task_id = (int)$_GET['edit']; // taak id (opnieuw) ophalen
    $stmt = $tasks_db->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
    $task_to_edit = $stmt->fetch(); // taak inhoud ophalen
}

// Handle task update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task'])) {
    $task_id = (int)$_POST['task_id']; // taak id (weer) ophalen
    $title = trim($_POST['title']); // Updated task title
    $description = trim($_POST['description']); // nieuwe taak beschrijving
    $due_date = $_POST['due_date'] ?? null; // nieuwe inleverdatum

    if (empty($title)) {
        $error = 'Task title is required.'; // error voor lege titel
    } else {
        // taak updaten in beschrijving
        $stmt = $tasks_db->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $description, $due_date, $task_id, $user_id]);
        $success = 'Task updated successfully!';
        $task_to_edit = null; // weggaan uit editen
    }
}

// data voor user ophalen
$stmt = $tasks_db->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC, created_at DESC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(); // alle bijbehorende taken ophalen
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link rel="stylesheet" href="css/style.css"> <!-- externe css -->
</head>
<body>
    <div class="task-container">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <a href="logout.php" class="logout-button">Logout</a> <!-- Logout link -->
		
		<a href="agenda.php" class="agenda-button">View Agenda</a> <!-- link naar de agenda pagina (is later toegevoegd aan het bestand) -->


        <h2>Your Tasks</h2>
        <?php if ($error): ?> <!-- error bericht -->
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?> <!-- error bericht -->
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <!-- taak form maken / bewerken -->
        <form method="POST" class="task-form">
            <input type="hidden" name="task_id" value="<?= $task_to_edit['id'] ?? '' ?>">
            <input type="text" name="title" placeholder="Task Title" value="<?= htmlspecialchars($task_to_edit['title'] ?? '') ?>" required>
            <textarea name="description" placeholder="Task Description"><?= htmlspecialchars($task_to_edit['description'] ?? '') ?></textarea>
            <input type="date" name="due_date" value="<?= htmlspecialchars($task_to_edit['due_date'] ?? '') ?>">
            
            <?php if ($task_to_edit): ?>
                <button type="submit" name="edit_task">Update Task</button>
                <a href="tasks.php" class="cancel-button">Cancel</a> <!-- edit mode anulleren -->
            <?php else: ?>
                <button type="submit" name="create_task">Add Task</button>
            <?php endif; ?>
        </form>

        <!-- taak limiet -->
        <ul class="task-list">
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                    <li>
                        <h3><?= htmlspecialchars($task['title']) ?></h3>
                        <p><?= htmlspecialchars($task['description']) ?></p>
                        <p><strong>Due:</strong> <?= htmlspecialchars($task['due_date'] ?? 'No due date') ?></p>
                        <a href="tasks.php?edit=<?= $task['id'] ?>" class="edit-button">Edit</a>
                        <a href="tasks.php?delete=<?= $task['id'] ?>" class="delete-button">Delete</a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tasks found. Add your first task above!</p>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
