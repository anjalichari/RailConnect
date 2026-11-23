<?php
// Set page title and load database connection and header
$page_title = "Compulsory SQL Reports";
require_once "includes/db.php";
require_once "includes/header.php";
?>

<div class="page-header">
    <h1 class="page-title">Compulsory SQL Query Reports</h1>
    <p class="page-description">Demonstration of the five compulsory SQL queries for the College DBMS Project evaluation.</p>
</div>

<!-- ========================================================================= -->
<!-- Query A -->
<!-- ========================================================================= -->
<div class="report-section">
    <h2 class="report-title">
        <span class="badge">Query A</span>
        Display passenger names along with train names using JOIN
    </h2>
    <div class="query-box">SELECT Passenger.passenger_name, Train.train_name
FROM Ticket
JOIN Passenger ON Ticket.passenger_id=Passenger.passenger_id
JOIN Train ON Ticket.train_no=Train.train_no;</div>

    <?php
    $sql_a = "SELECT Passenger.passenger_name, Train.train_name
              FROM Ticket
              JOIN Passenger ON Ticket.passenger_id=Passenger.passenger_id
              JOIN Train ON Ticket.train_no=Train.train_no";
    $result_a = mysqli_query($conn, $sql_a);
    ?>

    <?php if ($result_a && mysqli_num_rows($result_a) > 0): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Passenger Name</th>
                        <th>Train Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr = 1;
                    while ($row = mysqli_fetch_assoc($result_a)): 
                    ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['passenger_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['train_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No matching records found. Please ensure sample tickets and passengers exist.</div>
    <?php endif; ?>
</div>

<!-- ========================================================================= -->
<!-- Query B -->
<!-- ========================================================================= -->
<div class="report-section">
    <h2 class="report-title">
        <span class="badge">Query B</span>
        Display passengers who booked tickets having fare greater than average ticket fare
    </h2>
    <div class="query-box">SELECT DISTINCT Passenger.passenger_name, Ticket.fare
FROM Passenger
JOIN Ticket ON Passenger.passenger_id=Ticket.passenger_id
WHERE Ticket.fare>(SELECT AVG(fare) FROM Ticket);</div>

    <?php
    $sql_b = "SELECT DISTINCT Passenger.passenger_name, Ticket.fare
              FROM Passenger
              JOIN Ticket ON Passenger.passenger_id=Ticket.passenger_id
              WHERE Ticket.fare>(SELECT AVG(fare) FROM Ticket)";
    $result_b = mysqli_query($conn, $sql_b);

    // Also get the average fare for transparent presentation to the professor
    $avg_res = mysqli_query($conn, "SELECT AVG(fare) AS avg_fare FROM Ticket");
    $avg_fare = 0;
    if ($avg_res && $avg_row = mysqli_fetch_assoc($avg_res)) {
        $avg_fare = $avg_row['avg_fare'];
    }
    ?>

    <p style="font-size: 13px; color: #475569; margin-bottom: 10px;">
        <em>Current Average Ticket Fare: <strong>&#8377;<?php echo number_format((float)$avg_fare, 2); ?></strong></em>
    </p>

    <?php if ($result_b && mysqli_num_rows($result_b) > 0): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Passenger Name</th>
                        <th>Ticket Fare (&#8377;)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr = 1;
                    while ($row = mysqli_fetch_assoc($result_b)): 
                    ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['passenger_name']); ?></strong></td>
                            <td>&#8377;<?php echo number_format($row['fare'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No matching records found. Ensure tickets exist with fares above the average.</div>
    <?php endif; ?>
</div>

<!-- ========================================================================= -->
<!-- Query C -->
<!-- ========================================================================= -->
<div class="report-section">
    <h2 class="report-title">
        <span class="badge">Query C</span>
        Display trains whose names LIKE '%Express%'
    </h2>
    <div class="query-box">SELECT *
FROM Train
WHERE train_name LIKE '%Express%';</div>

    <?php
    $sql_c = "SELECT *
              FROM Train
              WHERE train_name LIKE '%Express%'";
    $result_c = mysqli_query($conn, $sql_c);
    ?>

    <?php if ($result_c && mysqli_num_rows($result_c) > 0): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Train Number</th>
                        <th>Train Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_c)): ?>
                        <tr>
                            <td><strong><?php echo (int)$row['train_no']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['train_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No trains matching '%Express%' found.</div>
    <?php endif; ?>
</div>

<!-- ========================================================================= -->
<!-- Query D -->
<!-- ========================================================================= -->
<div class="report-section">
    <h2 class="report-title">
        <span class="badge">Query D</span>
        Display number of passengers travelling in each train using GROUP BY and COUNT()
    </h2>
    <div class="query-box">SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
FROM Train
LEFT JOIN Ticket ON Train.train_no=Ticket.train_no
GROUP BY Train.train_no, Train.train_name;</div>

    <?php
    $sql_d = "SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
              FROM Train
              LEFT JOIN Ticket ON Train.train_no=Ticket.train_no
              GROUP BY Train.train_no, Train.train_name";
    $result_d = mysqli_query($conn, $sql_d);
    ?>

    <?php if ($result_d && mysqli_num_rows($result_d) > 0): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Train Name</th>
                        <th style="width: 200px; text-align: center;">Passenger Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr = 1;
                    while ($row = mysqli_fetch_assoc($result_d)): 
                    ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['train_name']); ?></strong></td>
                            <td style="text-align: center;">
                                <span style="font-weight: bold; background-color: #e2e8f0; padding: 3px 12px; border-radius: 12px;">
                                    <?php echo (int)$row['passenger_count']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No train records found.</div>
    <?php endif; ?>
</div>

<!-- ========================================================================= -->
<!-- Query E -->
<!-- ========================================================================= -->
<div class="report-section">
    <h2 class="report-title">
        <span class="badge">Query E</span>
        Display trains having more than 100 passengers using HAVING
    </h2>
    <div class="query-box">SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
FROM Train
JOIN Ticket ON Train.train_no=Ticket.train_no
GROUP BY Train.train_no, Train.train_name
HAVING COUNT(DISTINCT Ticket.passenger_id)>100;</div>

    <?php
    $sql_e = "SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
              FROM Train
              JOIN Ticket ON Train.train_no=Ticket.train_no
              GROUP BY Train.train_no, Train.train_name
              HAVING COUNT(DISTINCT Ticket.passenger_id)>100";
    $result_e = mysqli_query($conn, $sql_e);
    ?>

    <?php if ($result_e && mysqli_num_rows($result_e) > 0): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Train Name</th>
                        <th style="width: 220px; text-align: center;">Distinct Passenger Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sr = 1;
                    while ($row = mysqli_fetch_assoc($result_e)): 
                    ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['train_name']); ?></strong></td>
                            <td style="text-align: center;">
                                <span style="font-weight: bold; color: #137333; background-color: #e6f4ea; padding: 4px 14px; border-radius: 12px;">
                                    <?php echo (int)$row['passenger_count']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <strong>No matching records found with &gt; 100 passengers in regular sample data.</strong><br>
            Under normal operational data, trains have fewer than 100 sample passengers to keep the database lightweight.<br>
            To demonstrate this query with &gt; 100 passengers, import <strong>database/having_demo.sql</strong> in phpMyAdmin.
        </div>
    <?php endif; ?>
</div>

<?php
require_once "includes/footer.php";
?>
