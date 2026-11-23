<?php
// ===================================================
// RailConnect: Database Connection
// Technology: Procedural PHP with MySQLi
// Default XAMPP Configuration
// ===================================================

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "railway_reservation";

// Create connection using mysqli_connect
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check if connection was successful
if (!$conn) {
    die("<div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
            <h3>Database Connection Failed!</h3>
            <p><strong>Error:</strong> " . mysqli_connect_error() . "</p>
            <p>Please make sure:</p>
            <ul>
                <li>Apache and MySQL are running in your XAMPP Control Panel.</li>
                <li>The database <strong>railway_reservation</strong> is created in phpMyAdmin.</li>
                <li>The database schema <strong>database/railway_reservation.sql</strong> has been imported.</li>
            </ul>
         </div>");
}

// Set character set to utf8mb4 for unicode support
mysqli_set_charset($conn, "utf8mb4");
?>
