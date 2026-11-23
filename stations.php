<?php
// Set page title and load database connection and header
$page_title = "Manage Stations";
require_once "includes/db.php";
require_once "includes/header.php";

$success_msg = "";
$error_msg = "";

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delete_no = (int)$_GET['delete'];

    // Check foreign key reference: prevent delete if station is source or destination in tickets
    $stmt_check = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM Ticket WHERE source_station = ? OR destination_station = ?");
    mysqli_stmt_bind_param($stmt_check, "ii", $delete_no, $delete_no);
    mysqli_stmt_execute($stmt_check);
    $check_result = mysqli_stmt_get_result($stmt_check);
    $check_row = mysqli_fetch_assoc($check_result);
    mysqli_stmt_close($stmt_check);

    if ($check_row['cnt'] > 0) {
        $error_msg = "Cannot delete station (No: " . $delete_no . "). This station is used as source or destination in existing tickets. Please delete those tickets first.";
    } else {
        $stmt_del = mysqli_prepare($conn, "DELETE FROM Station WHERE station_no = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $delete_no);
        if (mysqli_stmt_execute($stmt_del)) {
            $success_msg = "Station record deleted successfully.";
        } else {
            $error_msg = "Failed to delete station: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_del);
    }
}

// Handle Form Submission (Add or Update)
$edit_mode = false;
$station_no = "";
$station_name = "";
$location = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $station_name = trim($_POST['station_name']);
    $location = trim($_POST['location']);

    if ($action === 'add') {
        $station_no = (int)$_POST['station_no'];

        // Validation
        if ($station_no <= 0) {
            $error_msg = "Please enter a valid positive Station Number.";
        } elseif (empty($station_name)) {
            $error_msg = "Station name is required.";
        } else {
            // Check if station_no already exists
            $stmt_check = mysqli_prepare($conn, "SELECT station_no FROM Station WHERE station_no = ?");
            mysqli_stmt_bind_param($stmt_check, "i", $station_no);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $error_msg = "Station number " . $station_no . " already exists. Please choose a different number.";
            } else {
                // Insert new station
                $stmt_insert = mysqli_prepare($conn, "INSERT INTO Station (station_no, station_name, location) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt_insert, "iss", $station_no, $station_name, $location);
                if (mysqli_stmt_execute($stmt_insert)) {
                    $success_msg = "New station added successfully!";
                    $station_no = "";
                    $station_name = "";
                    $location = "";
                } else {
                    $error_msg = "Error adding station: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt_insert);
            }
            mysqli_stmt_close($stmt_check);
        }
    } elseif ($action === 'update') {
        $station_no = (int)$_POST['station_no'];

        if (empty($station_name)) {
            $error_msg = "Station name cannot be empty.";
            $edit_mode = true;
        } else {
            // Update station details
            $stmt_update = mysqli_prepare($conn, "UPDATE Station SET station_name = ?, location = ? WHERE station_no = ?");
            mysqli_stmt_bind_param($stmt_update, "ssi", $station_name, $location, $station_no);
            if (mysqli_stmt_execute($stmt_update)) {
                $success_msg = "Station details updated successfully!";
                $edit_mode = false;
                $station_no = "";
                $station_name = "";
                $location = "";
            } else {
                $error_msg = "Error updating station: " . mysqli_error($conn);
                $edit_mode = true;
            }
            mysqli_stmt_close($stmt_update);
        }
    }
}

// Check if Edit requested via GET
if (isset($_GET['edit']) && empty($error_msg)) {
    $edit_no = (int)$_GET['edit'];
    $stmt_get = mysqli_prepare($conn, "SELECT * FROM Station WHERE station_no = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $edit_no);
    mysqli_stmt_execute($stmt_get);
    $res_get = mysqli_stmt_get_result($stmt_get);
    if ($row_get = mysqli_fetch_assoc($res_get)) {
        $edit_mode = true;
        $station_no = $row_get['station_no'];
        $station_name = $row_get['station_name'];
        $location = $row_get['location'];
    } else {
        $error_msg = "Station not found.";
    }
    mysqli_stmt_close($stmt_get);
}

// Fetch all stations
$result = mysqli_query($conn, "SELECT * FROM Station ORDER BY station_no ASC");
?>

<div class="page-header">
    <h1 class="page-title">Station Management</h1>
    <p class="page-description">Add new railway stations, edit station locations, and manage station master data.</p>
</div>

<!-- Alert Messages -->
<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<!-- Station Form Card -->
<div class="card">
    <h2 class="card-title"><?php echo $edit_mode ? "Edit Station Details (Station No: " . htmlspecialchars($station_no) . ")" : "Add New Station"; ?></h2>
    <form method="POST" action="stations.php">
        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="station_no">Station Number *</label>
                <input type="number" id="station_no" name="station_no" class="form-control" required 
                       value="<?php echo htmlspecialchars($station_no); ?>" 
                       <?php echo $edit_mode ? 'readonly style="background-color: #f1f5f9;"' : 'placeholder="e.g. 101"'; ?>>
                <?php if ($edit_mode): ?>
                    <small style="color: #64748b; margin-top: 3px;">Station Number is the Primary Key and cannot be changed.</small>
                <?php endif; ?>
            </div>

            <div class="form-group" style="flex: 2;">
                <label for="station_name">Station Name *</label>
                <input type="text" id="station_name" name="station_name" class="form-control" required 
                       value="<?php echo htmlspecialchars($station_name); ?>" placeholder="e.g. Chhatrapati Shivaji Terminus">
            </div>

            <div class="form-group" style="flex: 1.5;">
                <label for="location">City / Location</label>
                <input type="text" id="location" name="location" class="form-control" 
                       value="<?php echo htmlspecialchars($location); ?>" placeholder="e.g. Mumbai">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $edit_mode ? "Update Station" : "Add Station"; ?>
            </button>
            <?php if ($edit_mode): ?>
                <a href="stations.php" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Station List Table -->
<div class="card">
    <h2 class="card-title">All Stations List</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Station Number</th>
                    <th>Station Name</th>
                    <th>Location</th>
                    <th style="width: 140px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong><?php echo (int)$row['station_no']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['station_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td style="text-align: center;">
                                <div class="action-buttons" style="justify-content: center;">
                                    <a href="stations.php?edit=<?php echo (int)$row['station_no']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="stations.php?delete=<?php echo (int)$row['station_no']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete station <?php echo htmlspecialchars(addslashes($row['station_name'])); ?> (No: <?php echo (int)$row['station_no']; ?>)?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">
                            No stations registered yet. Use the form above to add a station.
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
