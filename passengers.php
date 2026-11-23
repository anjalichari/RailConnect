<?php
// Set page title and load database connection and header
$page_title = "Manage Passengers";
require_once "includes/db.php";
require_once "includes/header.php";

$success_msg = "";
$error_msg = "";

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // Check foreign key reference: prevent delete if passenger has booked tickets
    $stmt_check = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM Ticket WHERE passenger_id = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $delete_id);
    mysqli_stmt_execute($stmt_check);
    $check_result = mysqli_stmt_get_result($stmt_check);
    $check_row = mysqli_fetch_assoc($check_result);
    mysqli_stmt_close($stmt_check);

    if ($check_row['cnt'] > 0) {
        $error_msg = "Cannot delete passenger (ID: " . $delete_id . "). This passenger has active ticket reservation(s). Please delete the associated tickets first.";
    } else {
        $stmt_del = mysqli_prepare($conn, "DELETE FROM Passenger WHERE passenger_id = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $delete_id);
        if (mysqli_stmt_execute($stmt_del)) {
            $success_msg = "Passenger record deleted successfully.";
        } else {
            $error_msg = "Failed to delete passenger: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_del);
    }
}

// Handle Form Submission (Add or Update)
$edit_mode = false;
$edit_id = 0;
$passenger_name = "";
$gender = "Male";
$age = "";
$phone = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $passenger_name = trim($_POST['passenger_name']);
    $gender = trim($_POST['gender']);
    $age = (int)$_POST['age'];
    $phone = trim($_POST['phone']);

    // Basic Validation
    if (empty($passenger_name)) {
        $error_msg = "Passenger name is required.";
    } elseif ($age <= 0 || $age > 120) {
        $error_msg = "Please enter a valid age between 1 and 120.";
    } elseif (empty($phone)) {
        $error_msg = "Phone number is required.";
    } else {
        if ($action === 'add') {
            // Insert new passenger using prepared statement
            $stmt = mysqli_prepare($conn, "INSERT INTO Passenger (passenger_name, gender, age, phone) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssis", $passenger_name, $gender, $age, $phone);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "New passenger registered successfully!";
                // Reset form values
                $passenger_name = "";
                $gender = "Male";
                $age = "";
                $phone = "";
            } else {
                $error_msg = "Error registering passenger: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } elseif ($action === 'update') {
            $edit_id = (int)$_POST['passenger_id'];
            // Update existing passenger using prepared statement
            $stmt = mysqli_prepare($conn, "UPDATE Passenger SET passenger_name = ?, gender = ?, age = ?, phone = ? WHERE passenger_id = ?");
            mysqli_stmt_bind_param($stmt, "ssisi", $passenger_name, $gender, $age, $phone, $edit_id);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Passenger details updated successfully!";
                $edit_mode = false;
                $passenger_name = "";
                $gender = "Male";
                $age = "";
                $phone = "";
            } else {
                $error_msg = "Error updating passenger: " . mysqli_error($conn);
                $edit_mode = true;
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Check if Edit requested via GET
if (isset($_GET['edit']) && empty($error_msg)) {
    $edit_id = (int)$_GET['edit'];
    $stmt_get = mysqli_prepare($conn, "SELECT * FROM Passenger WHERE passenger_id = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $edit_id);
    mysqli_stmt_execute($stmt_get);
    $res_get = mysqli_stmt_get_result($stmt_get);
    if ($row_get = mysqli_fetch_assoc($res_get)) {
        $edit_mode = true;
        $passenger_name = $row_get['passenger_name'];
        $gender = $row_get['gender'];
        $age = $row_get['age'];
        $phone = $row_get['phone'];
    } else {
        $error_msg = "Passenger not found.";
    }
    mysqli_stmt_close($stmt_get);
}

// Fetch all passenger records
$result = mysqli_query($conn, "SELECT * FROM Passenger ORDER BY passenger_id ASC");
?>

<div class="page-header">
    <h1 class="page-title">Passenger Management</h1>
    <p class="page-description">Add new passengers, update passenger records, and view the passenger database.</p>
</div>

<!-- Alert Messages -->
<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<!-- Passenger Form Card -->
<div class="card">
    <h2 class="card-title"><?php echo $edit_mode ? "Edit Passenger Details (ID: " . $edit_id . ")" : "Add New Passenger"; ?></h2>
    <form method="POST" action="passengers.php">
        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="passenger_id" value="<?php echo $edit_id; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="passenger_name">Full Name *</label>
                <input type="text" id="passenger_name" name="passenger_name" class="form-control" required 
                       value="<?php echo htmlspecialchars($passenger_name); ?>" placeholder="e.g. Rahul Sharma">
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" class="form-control">
                    <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($gender === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="age">Age *</label>
                <input type="number" id="age" name="age" class="form-control" min="1" max="120" required 
                       value="<?php echo htmlspecialchars($age); ?>" placeholder="e.g. 28">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="text" id="phone" name="phone" class="form-control" required 
                       value="<?php echo htmlspecialchars($phone); ?>" placeholder="e.g. 9876543210">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $edit_mode ? "Update Passenger" : "Add Passenger"; ?>
            </button>
            <?php if ($edit_mode): ?>
                <a href="passengers.php" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Passengers List Table -->
<div class="card">
    <h2 class="card-title">All Passengers List</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Passenger Name</th>
                    <th style="width: 100px;">Gender</th>
                    <th style="width: 80px;">Age</th>
                    <th>Phone</th>
                    <th style="width: 140px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo (int)$row['passenger_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['passenger_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo (int)$row['age']; ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td style="text-align: center;">
                                <div class="action-buttons" style="justify-content: center;">
                                    <a href="passengers.php?edit=<?php echo (int)$row['passenger_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="passengers.php?delete=<?php echo (int)$row['passenger_id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete passenger <?php echo htmlspecialchars(addslashes($row['passenger_name'])); ?>?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b; padding: 20px;">
                            No passenger records found. Use the form above to add one.
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
