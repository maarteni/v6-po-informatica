<?php
session_start();
require 'includes/db_connect.php';

// ingelogd zijn
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// user id
$user_id = $_SESSION['user_id'];

// taken met inleverdatums
$stmt = $tasks_db->prepare("SELECT title, due_date FROM tasks WHERE user_id = ? AND due_date IS NOT NULL ORDER BY due_date ASC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

// jaar en maand
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// eerste lagen dagen van de maand (vervelend)
$first_day = strtotime("$year-$month-01");
$last_day = strtotime("last day of", $first_day);

// aantal dagen in een maand (dit was kut om uit te zoeken)
$days_in_month = date('t', $first_day);

//
$tasks_by_date = [];
foreach ($tasks as $task) {
    $tasks_by_date[$task['due_date']][] = $task['title'];
}

// maanden en jaren voor duidelijke kalender
$prev_month = $month - 1 < 1 ? 12 : $month - 1;
$prev_year = $month - 1 < 1 ? $year - 1 : $year;

$next_month = $month + 1 > 12 ? 1 : $month + 1;
$next_year = $month + 1 > 12 ? $year + 1 : $year;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda</title>
    <link rel="stylesheet" href="css/style.css"> <!-- externe css -->
</head>
<body>
    <div class="agenda-container">
        <h1>Your Agenda</h1>
        <a href="tasks.php" class="back-button">Back to Tasks</a> <!-- terug naar taken pagina -->

        <!-- maanden enz -->
        <div class="calendar-navigation">
            <a href="agenda.php?month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="nav-button">Previous</a>
            <span><?= date('F Y', $first_day) ?></span>
            <a href="agenda.php?month=<?= $next_month ?>&year=<?= $next_year ?>" class="nav-button">Next</a>
        </div>

        <!-- kalender rooster  -->
        <div class="calendar-grid">
            <!-- dagen van de week -->
            <div class="calendar-header">Sun</div>
            <div class="calendar-header">Mon</div>
            <div class="calendar-header">Tue</div>
            <div class="calendar-header">Wed</div>
            <div class="calendar-header">Thu</div>
            <div class="calendar-header">Fri</div>
            <div class="calendar-header">Sat</div>

            <?php
            // eerste dagen van de maand regelen
            $start_day = date('w', $first_day);
            for ($i = 0; $i < $start_day; $i++): ?>
                <div class="calendar-cell empty"></div>
            <?php endfor; ?>

            <!-- dagen met  taken -->
            <?php for ($day = 1; $day <= $days_in_month; $day++): 
                $date = date('Y-m-d', strtotime("$year-$month-$day"));
                ?>
                <div class="calendar-cell">
                    <div class="date"><?= $day ?></div>
                    <?php if (isset($tasks_by_date[$date])): ?>
                        <ul class="task-list">
                            <?php foreach ($tasks_by_date[$date] as $task_title): ?>
                                <li><?= htmlspecialchars($task_title) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
