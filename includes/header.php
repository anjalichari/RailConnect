<?php
// Determine the current page for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - RailConnect' : 'RailConnect - Railway Reservation Management System'; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Main Header & Navigation -->
    <header class="site-header">
        <div class="header-inner">
            <div class="brand">
                <a href="index.php" class="brand-title">RailConnect</a>
                <span class="brand-tagline">Your Journey, Our Management</span>
            </div>
            <nav class="nav-menu">
                <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                <a href="passengers.php" class="nav-link <?php echo ($current_page == 'passengers.php') ? 'active' : ''; ?>">Passengers</a>
                <a href="trains.php" class="nav-link <?php echo ($current_page == 'trains.php') ? 'active' : ''; ?>">Trains</a>
                <a href="stations.php" class="nav-link <?php echo ($current_page == 'stations.php') ? 'active' : ''; ?>">Stations</a>
                <a href="tickets.php" class="nav-link <?php echo ($current_page == 'tickets.php') ? 'active' : ''; ?>">Tickets</a>
                <a href="reports.php" class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">Reports</a>
            </nav>
        </div>
    </header>

    <!-- Page Content Container -->
    <main class="container main-content">
