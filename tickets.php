<?php
// Set page title and load database connection and header
$page_title = "Manage Tickets";
require_once "includes/db.php";
require_once "includes/header.php";

$success_msg = "";
$error_msg = "";

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt_del = mysqli_prepare($conn, "DELETE FROM Ticket WHERE ticket_id = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $delete_id);
    if (mysqli_stmt_execute($stmt_del)) {
        $success_msg = "Ticket (ID: " . $delete_id . ") canceled and deleted successfully.";
    } else {
        $error_msg = "Failed to delete ticket: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt_del);
}

// Handle Form Submission (Add or Update)
$edit_mode = false;
$edit_id = 0;
$passenger_id = "";
$train_no = "";
$source_station = "";
$destination_station = "";
$journey_date = date('Y-m-d');
$fare = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $passenger_id = (int)$_POST['passenger_id'];
    $train_no = (int)$_POST['train_no'];
    $source_station = (int)$_POST['source_station'];
    $destination_station = (int)$_POST['destination_station'];
    $journey_date = trim($_POST['journey_date']);
    $fare = floatval($_POST['fare']);

    // Basic Validations
    if ($passenger_id <= 0) {
        $error_msg = "Please select a passenger.";
    } elseif ($train_no <= 0) {
        $error_msg = "Please select a train.";
    } elseif ($source_station <= 0) {
        $error_msg = "Please select a source station.";
    } elseif ($destination_station <= 0) {
        $error_msg = "Please select a destination station.";
    } elseif ($source_station === $destination_station) {
        $error_msg = "Source station and destination station cannot be the same.";
    } elseif (empty($journey_date)) {
        $error_msg = "Journey date is required.";
    } elseif ($fare <= 0) {
        $error_msg = "Please enter a valid ticket fare greater than 0.";
    } else {
        if ($action === 'add') {
            // Insert new ticket using prepared statement
            $stmt = mysqli_prepare($conn, "INSERT INTO Ticket (passenger_id, train_no, source_station, destination_station, journey_date, fare) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiisd", $passenger_id, $train_no, $source_station, $destination_station, $journey_date, $fare);
            if (mysqli_stmt_execute($stmt)) {
                $new_ticket_id = mysqli_insert_id($conn);
                $success_msg = "Ticket booked successfully! Ticket ID is #" . $new_ticket_id;
                // Reset form fields
                $passenger_id = "";
                $train_no = "";
                $source_station = "";
                $destination_station = "";
                $journey_date = date('Y-m-d');
                $fare = "";
            } else {
                $error_msg = "Error booking ticket: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } elseif ($action === 'update') {
            $edit_id = (int)$_POST['ticket_id'];
            // Update existing ticket
            $stmt = mysqli_prepare($conn, "UPDATE Ticket SET passenger_id = ?, train_no = ?, source_station = ?, destination_station = ?, journey_date = ?, fare = ? WHERE ticket_id = ?");
            mysqli_stmt_bind_param($stmt, "iiiisdi", $passenger_id, $train_no, $source_station, $destination_station, $journey_date, $fare, $edit_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Ticket #" . $edit_id . " updated successfully!";
                $edit_mode = false;
                $passenger_id = "";
                $train_no = "";
                $source_station = "";
                $destination_station = "";
                $journey_date = date('Y-m-d');
                $fare = "";
            } else {
                $error_msg = "Error updating ticket: " . mysqli_error($conn);
                $edit_mode = true;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Check if Edit requested via GET
if (isset($_GET['edit']) && empty($error_msg)) {
    $edit_id = (int)$_GET['edit'];
    $stmt_get = mysqli_prepare($conn, "SELECT * FROM Ticket WHERE ticket_id = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $edit_id);
    mysqli_stmt_execute($stmt_get);
    $res_get = mysqli_stmt_get_result($stmt_get);
    if ($row_get = mysqli_fetch_assoc($res_get)) {
        $edit_mode = true;
        $passenger_id = $row_get['passenger_id'];
        $train_no = $row_get['train_no'];
        $source_station = $row_get['source_station'];
        $destination_station = $row_get['destination_station'];
        $journey_date = $row_get['journey_date'];
        $fare = $row_get['fare'];
    } else {
        $error_msg = "Ticket not found.";
    }
    mysqli_stmt_close($stmt_get);
}

// Fetch Dropdown Data
$passengers_res = mysqli_query($conn, "SELECT passenger_id, passenger_name FROM Passenger ORDER BY passenger_name ASC");
$trains_res = mysqli_query($conn, "SELECT train_no, train_name FROM Train ORDER BY train_name ASC");
$stations_res1 = mysqli_query($conn, "SELECT station_no, station_name, location FROM Station ORDER BY station_name ASC");
$stations_res2 = mysqli_query($conn, "SELECT station_no, station_name, location FROM Station ORDER BY station_name ASC");

// Fetch All Tickets with JOINs for Readable Names
$tickets_query = "SELECT t.ticket_id, t.journey_date, t.fare,
                         p.passenger_id, p.passenger_name,
                         tr.train_no, tr.train_name,
                         s1.station_name AS source_name, s1.location AS source_loc,
                         s2.station_name AS dest_name, s2.location AS dest_loc
                  FROM Ticket t
                  JOIN Passenger p ON t.passenger_id = p.passenger_id
                  JOIN Train tr ON t.train_no = tr.train_no
                  JOIN Station s1 ON t.source_station = s1.station_no
                  JOIN Station s2 ON t.destination_station = s2.station_no
                  ORDER BY t.ticket_id DESC";
$tickets_result = mysqli_query($conn, $tickets_query);
?>

<div class="page-header">
    <h1 class="page-title">Ticket Booking &amp; Reservation</h1>
    <p class="page-description">Book train tickets, update reservation details, and view the ticket bookings list.</p>
</div>

<!-- Alert Messages -->
<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<!-- Ticket Booking Form Card -->
<div class="card">
    <h2 class="card-title"><?php echo $edit_mode ? "Update Reservation Details (Ticket ID: #" . $edit_id . ")" : "Book a New Ticket"; ?></h2>
    <form method="POST" action="tickets.php">
        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="ticket_id" value="<?php echo $edit_id; ?>">
        <?php endif; ?>

        <div class="form-row">
            <!-- Select Passenger -->
            <div class="form-group">
                <label for="passenger_id">Passenger *</label>
                <select id="passenger_id" name="passenger_id" class="form-control" required>
                    <option value="">-- Select Passenger --</option>
                    <?php 
                    if ($passengers_res && mysqli_num_rows($passengers_res) > 0) {
                        mysqli_data_seek($passengers_res, 0);
                        while ($p = mysqli_fetch_assoc($passengers_res)) {
                            $selected = ($passenger_id == $p['passenger_id']) ? 'selected' : '';
                            echo '<option value="' . (int)$p['passenger_id'] . '" ' . $selected . '>' 
                                 . htmlspecialchars($p['passenger_name']) . ' (ID: ' . (int)$p['passenger_id'] . ')' 
                                 . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Select Train -->
            <div class="form-group">
                <label for="train_no">Train *</label>
                <select id="train_no" name="train_no" class="form-control" required>
                    <option value="">-- Select Train --</option>
                    <?php 
                    if ($trains_res && mysqli_num_rows($trains_res) > 0) {
                        mysqli_data_seek($trains_res, 0);
                        while ($tr = mysqli_fetch_assoc($trains_res)) {
                            $selected = ($train_no == $tr['train_no']) ? 'selected' : '';
                            echo '<option value="' . (int)$tr['train_no'] . '" ' . $selected . '>' 
                                 . htmlspecialchars($tr['train_name']) . ' (No: ' . (int)$tr['train_no'] . ')' 
                                 . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <!-- Select Source Station -->
            <div class="form-group">
                <label for="source_station">Source Station *</label>
                <select id="source_station" name="source_station" class="form-control" required>
                    <option value="">-- Select Source Station --</option>
                    <?php 
                    if ($stations_res1 && mysqli_num_rows($stations_res1) > 0) {
                        mysqli_data_seek($stations_res1, 0);
                        while ($s = mysqli_fetch_assoc($stations_res1)) {
                            $selected = ($source_station == $s['station_no']) ? 'selected' : '';
                            echo '<option value="' . (int)$s['station_no'] . '" ' . $selected . '>' 
                                 . htmlspecialchars($s['station_name']) . ' [' . htmlspecialchars($s['location']) . ']' 
                                 . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Select Destination Station -->
            <div class="form-group">
                <label for="destination_station">Destination Station *</label>
                <select id="destination_station" name="destination_station" class="form-control" required>
                    <option value="">-- Select Destination Station --</option>
                    <?php 
                    if ($stations_res2 && mysqli_num_rows($stations_res2) > 0) {
                        mysqli_data_seek($stations_res2, 0);
                        while ($s = mysqli_fetch_assoc($stations_res2)) {
                            $selected = ($destination_station == $s['station_no']) ? 'selected' : '';
                            echo '<option value="' . (int)$s['station_no'] . '" ' . $selected . '>' 
                                 . htmlspecialchars($s['station_name']) . ' [' . htmlspecialchars($s['location']) . ']' 
                                 . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <!-- Journey Date -->
            <div class="form-group">
                <label for="journey_date">Date of Journey *</label>
                <input type="date" id="journey_date" name="journey_date" class="form-control" required 
                       value="<?php echo htmlspecialchars($journey_date); ?>">
            </div>

            <!-- Ticket Fare -->
            <div class="form-group">
                <label for="fare">Fare (in INR &#8377;) *</label>
                <input type="number" id="fare" name="fare" class="form-control" step="0.01" min="1" required 
                       value="<?php echo htmlspecialchars($fare); ?>" placeholder="e.g. 1850.00">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $edit_mode ? "Update Reservation" : "Book Ticket"; ?>
            </button>
            <?php if ($edit_mode): ?>
                <a href="tickets.php" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- All Tickets Table -->
<div class="card">
    <h2 class="card-title">All Booked Tickets</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Ticket #</th>
                    <th>Passenger</th>
                    <th>Train</th>
                    <th>Source Station</th>
                    <th>Destination Station</th>
                    <th style="width: 110px;">Journey Date</th>
                    <th style="width: 100px;">Fare (&#8377;)</th>
                    <th style="width: 140px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tickets_result && mysqli_num_rows($tickets_result) > 0): ?>
                    <?php while ($t = mysqli_fetch_assoc($tickets_result)): ?>
                        <tr>
                            <td><strong>#<?php echo (int)$t['ticket_id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($t['passenger_name']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($t['train_name']); ?></strong><br>
                                <small style="color: #64748b;">(No: <?php echo (int)$t['train_no']; ?>)</small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($t['source_name']); ?><br>
                                <small style="color: #64748b;"><?php echo htmlspecialchars($t['source_loc']); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($t['dest_name']); ?><br>
                                <small style="color: #64748b;"><?php echo htmlspecialchars($t['dest_loc']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($t['journey_date']); ?></td>
                            <td>&#8377;<?php echo number_format($t['fare'], 2); ?></td>
                            <td style="text-align: center;">
                                <div class="action-buttons" style="justify-content: center;">
                                    <a href="tickets.php?edit=<?php echo (int)$t['ticket_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="tickets.php?delete=<?php echo (int)$t['ticket_id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to cancel and delete Ticket #<?php echo (int)$t['ticket_id']; ?>?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 20px;">
                            No tickets booked yet. Use the reservation form above to book a ticket.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once "includes/footer.php";
?>
