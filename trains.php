<?php
// Set page title and load database connection and header
$page_title = "Manage Trains";
require_once "includes/db.php";
require_once "includes/header.php";

$success_msg = "";
$error_msg = "";

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delete_no = (int)$_GET['delete'];

    // Check foreign key reference: prevent delete if tickets exist for this train
    $stmt_check = mysqli_prepare($conn, "SELECT COUNT(*) AS cnt FROM Ticket WHERE train_no = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $delete_no);
    mysqli_stmt_execute($stmt_check);
    $check_result = mysqli_stmt_get_result($stmt_check);
    $check_row = mysqli_fetch_assoc($check_result);
    mysqli_stmt_close($stmt_check);

    if ($check_row['cnt'] > 0) {
        $error_msg = "Cannot delete train (No: " . $delete_no . "). There are tickets booked for this train. Please delete the tickets first.";
    } else {
        $stmt_del = mysqli_prepare($conn, "DELETE FROM Train WHERE train_no = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $delete_no);
        if (mysqli_stmt_execute($stmt_del)) {
            $success_msg = "Train record deleted successfully.";
        } else {
            $error_msg = "Failed to delete train: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_del);
    }
}

// Handle Form Submission (Add or Update)
$edit_mode = false;
$train_no = "";
$train_name = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $train_name = trim($_POST['train_name']);

    if ($action === 'add') {
        $train_no = (int)$_POST['train_no'];

        // Validation
        if ($train_no <= 0) {
            $error_msg = "Please enter a valid positive Train Number.";
        } elseif (empty($train_name)) {
            $error_msg = "Train name is required.";
        } else {
            // Check if train_no already exists
            $stmt_check = mysqli_prepare($conn, "SELECT train_no FROM Train WHERE train_no = ?");
            mysqli_stmt_bind_param($stmt_check, "i", $train_no);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $error_msg = "Train number " . $train_no . " already exists. Please choose a different number.";
            } else {
                // Insert new train
                $stmt_insert = mysqli_prepare($conn, "INSERT INTO Train (train_no, train_name) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt_insert, "is", $train_no, $train_name);
                if (mysqli_stmt_execute($stmt_insert)) {
                    $success_msg = "New train added successfully!";
                    $train_no = "";
                    $train_name = "";
                } else {
                    $error_msg = "Error adding train: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt_insert);
            }
            mysqli_stmt_close($stmt_check);
        }
    } elseif ($action === 'update') {
        $train_no = (int)$_POST['train_no'];

        if (empty($train_name)) {
            $error_msg = "Train name cannot be empty.";
            $edit_mode = true;
        } else {
            // Update train name
            $stmt_update = mysqli_prepare($conn, "UPDATE Train SET train_name = ? WHERE train_no = ?");
            mysqli_stmt_bind_param($stmt_update, "si", $train_name, $train_no);
            if (mysqli_stmt_execute($stmt_update)) {
                $success_msg = "Train details updated successfully!";
                $edit_mode = false;
                $train_no = "";
                $train_name = "";
            } else {
                $error_msg = "Error updating train: " . mysqli_error($conn);
                $edit_mode = true;
            }
            mysqli_stmt_close($stmt_update);
        }
    }
}

// Check if Edit requested via GET
if (isset($_GET['edit']) && empty($error_msg)) {
    $edit_no = (int)$_GET['edit'];
    $stmt_get = mysqli_prepare($conn, "SELECT * FROM Train WHERE train_no = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $edit_no);
    mysqli_stmt_execute($stmt_get);
    $res_get = mysqli_stmt_get_result($stmt_get);
    if ($row_get = mysqli_fetch_assoc($res_get)) {
        $edit_mode = true;
        $train_no = $row_get['train_no'];
        $train_name = $row_get['train_name'];
    } else {
        $error_msg = "Train not found.";
    }
    mysqli_stmt_close($stmt_get);
}

// Fetch all trains
$result = mysqli_query($conn, "SELECT * FROM Train ORDER BY train_no ASC");
?>

<div class="page-header">
    <h1 class="page-title">Train Management</h1>
    <p class="page-description">Add new trains, update train information, and view the active train fleet.</p>
</div>

<!-- Alert Messages -->
<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<!-- Train Form Card -->
<div class="card">
    <h2 class="card-title"><?php echo $edit_mode ? "Edit Train Details (Train No: " . htmlspecialchars($train_no) . ")" : "Add New Train"; ?></h2>
    <form method="POST" action="trains.php">
        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="train_no">Train Number *</label>
                <input type="number" id="train_no" name="train_no" class="form-control" required 
                       value="<?php echo htmlspecialchars($train_no); ?>" 
                       <?php echo $edit_mode ? 'readonly style="background-color: #f1f5f9;"' : 'placeholder="e.g. 12951"'; ?>>
                <?php if ($edit_mode): ?>
                    <small style="color: #64748b; margin-top: 3px;">Train Number is the Primary Key and cannot be changed.</small>
                <?php endif; ?>
            </div>

            <div class="form-group" style="flex: 2;">
                <label for="train_name">Train Name *</label>
                <input type="text" id="train_name" name="train_name" class="form-control" required 
                       value="<?php echo htmlspecialchars($train_name); ?>" placeholder="e.g. Rajdhani Express">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?php echo $edit_mode ? "Update Train" : "Add Train"; ?>
            </button>
            <?php if ($edit_mode): ?>
                <a href="trains.php" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Train List Table -->
<div class="card">
    <h2 class="card-title">All Trains List</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 150px;">Train Number</th>
                    <th>Train Name</th>
                    <th style="width: 140px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong><?php echo (int)$row['train_no']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['train_name']); ?></td>
                            <td style="text-align: center;">
                                <div class="action-buttons" style="justify-content: center;">
                                    <a href="trains.php?edit=<?php echo (int)$row['train_no']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="trains.php?delete=<?php echo (int)$row['train_no']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete train <?php echo htmlspecialchars(addslashes($row['train_name'])); ?> (No: <?php echo (int)$row['train_no']; ?>)?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b; padding: 20px;">
                            No trains registered yet. Use the form above to add a train.
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
