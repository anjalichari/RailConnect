<?php
// Set page title and include database connection and header
$page_title = "Home";
require_once "includes/db.php";
require_once "includes/header.php";

// Fetch counts for summary cards
$passenger_count = 0;
$train_count = 0;
$station_count = 0;
$ticket_count = 0;

$res1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Passenger");
if ($res1 && $row = mysqli_fetch_assoc($res1)) {
    $passenger_count = $row['total'];
}

$res2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Train");
if ($res2 && $row = mysqli_fetch_assoc($res2)) {
    $train_count = $row['total'];
}

$res3 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Station");
if ($res3 && $row = mysqli_fetch_assoc($res3)) {
    $station_count = $row['total'];
}

$res4 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Ticket");
if ($res4 && $row = mysqli_fetch_assoc($res4)) {
    $ticket_count = $row['total'];
}
?>

<div class="page-header">
    <h1 class="page-title">Welcome to RailConnect</h1>
    <p class="page-description">Railway Reservation Management System &mdash; Third Year Computer Engineering DBMS Project</p>
</div>

<!-- Project Introduction Card -->
<div class="card">
    <h2 class="card-title">Project Overview</h2>
    <p style="margin-bottom: 10px;">
        <strong>RailConnect</strong> is a database management system designed to streamline and automate railway reservation operations. It manages passenger records, train schedules, railway stations, and ticket bookings using a relational MySQL database.
    </p>
    <p style="color: #475569; font-size: 14px;">
        This web application allows administrators and railway staff to perform Create, Read, Update, and Delete (CRUD) operations on all entities while maintaining data integrity through foreign key constraints in MySQL InnoDB.
    </p>
</div>

<!-- Record Count Statistics -->
<h2 style="font-size: 18px; color: #0f2a4a; margin-bottom: 15px;">System Overview &amp; Record Counts</h2>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Passengers</div>
        <div class="stat-number"><?php echo $passenger_count; ?></div>
        <a href="passengers.php" class="stat-link">Manage Passengers &rarr;</a>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Trains</div>
        <div class="stat-number"><?php echo $train_count; ?></div>
        <a href="trains.php" class="stat-link">Manage Trains &rarr;</a>
    </div>

    <div class="stat-card">
        <div class="stat-label">Total Stations</div>
        <div class="stat-number"><?php echo $station_count; ?></div>
        <a href="stations.php" class="stat-link">Manage Stations &rarr;</a>
    </div>

    <div class="stat-card">
        <div class="stat-label">Tickets Booked</div>
        <div class="stat-number"><?php echo $ticket_count; ?></div>
        <a href="tickets.php" class="stat-link">Manage Tickets &rarr;</a>
    </div>
</div>

<!-- Quick Navigation Links Card -->
<div class="card">
    <h2 class="card-title">Quick Actions</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="passengers.php" class="btn btn-primary">Add / View Passengers</a>
        <a href="trains.php" class="btn btn-primary">Add / View Trains</a>
        <a href="stations.php" class="btn btn-primary">Add / View Stations</a>
        <a href="tickets.php" class="btn btn-primary">Book New Ticket</a>
        <a href="reports.php" class="btn btn-secondary">View SQL Query Reports</a>
    </div>
</div>

<!-- Academic Information Card -->
<div class="card" style="background-color: #f8fafc;">
    <h2 class="card-title">Project Details</h2>
    <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; width: 180px; font-weight: bold; color: #334155;">Course:</td>
            <td>Database Management Systems (DBMS)</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #334155;">Database:</td>
            <td>MySQL (InnoDB engine with Foreign Key constraints)</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #334155;">Backend:</td>
            <td>Core Procedural PHP (MySQLi extension with Prepared Statements)</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold; color: #334155;">Frontend:</td>
            <td>Clean HTML5 &amp; CSS3 (Custom responsive styling)</td>
        </tr>
    </table>
</div>

<?php
require_once "includes/footer.php";
?>
