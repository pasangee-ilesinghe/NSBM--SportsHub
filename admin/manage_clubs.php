<?php
session_start();

// Strict Admin Role Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
require_once '../csrf.php';
csrf_protect();
$message = "";

// Variables to hold form state (Add vs. Edit)
$edit_mode = false;
$edit_club = [
    'id' => '',
    'club_name' => '',
    'description' => '',
    'master_in_charge' => '',
    'venue' => '',
    'email' => ''
];

// Check if we are editing an existing club (triggered by clicking "Edit")
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM clubs WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_club = $res->fetch_assoc();
        $edit_mode = true;
    }
}

// 1. Handle Add Club Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_club'])) {
    $club_name = trim($_POST['club_name']);
    $description = trim($_POST['description']);
    $master_in_charge = trim($_POST['master_in_charge']);
    $venue = trim($_POST['venue']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("INSERT INTO clubs (club_name, description, master_in_charge, venue, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $club_name, $description, $master_in_charge, $venue, $email);

    if ($stmt->execute()) {
        $message = "New sports club added successfully!";
    } else {
        $message = "Error adding club to database.";
    }
}

// 2. Handle Update Club Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_club'])) {
    $id = intval($_POST['club_id']);
    $club_name = trim($_POST['club_name']);
    $description = trim($_POST['description']);
    $master_in_charge = trim($_POST['master_in_charge']);
    $venue = trim($_POST['venue']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE clubs SET club_name = ?, description = ?, master_in_charge = ?, venue = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $club_name, $description, $master_in_charge, $venue, $email, $id);

    if ($stmt->execute()) {
        // Redirect back to clean management screen with a success state
        header("Location: manage_clubs.php?updated=1");
        exit();
    } else {
        $message = "Error updating club in database.";
        $edit_mode = true; // Keep edit mode active if failed
        $edit_club = ['id' => $id, 'club_name' => $club_name, 'description' => $description, 'master_in_charge' => $master_in_charge, 'venue' => $venue, 'email' => $email];
    }
}

// Check for URL redirect success states
if (isset($_GET['updated'])) {
    $message = "Sports club updated successfully!";
}

// 3. Handle Delete Club Action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['delete']);
    $delStmt = $conn->prepare("DELETE FROM clubs WHERE id = ?");
    $delStmt->bind_param("i", $id);
    $delStmt->execute();
    header("Location: manage_clubs.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Clubs - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Manage Sports Clubs</span>
    </nav>

    <div class="container my-5">
        <?php if($message): ?>
            <div class="alert alert-success py-2 text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Side: Add / Edit Club Form -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-primary mb-3">
                        <?php echo $edit_mode ? 'Edit Sports Club' : 'Add New Club'; ?>
                    </h4>
                    <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="update_club" value="1">
                            <input type="hidden" name="club_id" value="<?php echo $edit_club['id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="add_club" value="1">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Club Name</label>
                            <input type="text" name="club_name" class="form-control" placeholder="e.g. Cricket Club" value="<?php echo htmlspecialchars($edit_club['club_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief details..." required><?php echo htmlspecialchars($edit_club['description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Master-in-Charge</label>
                            <input type="text" name="master_in_charge" class="form-control" placeholder="e.g. Mr. John Doe" value="<?php echo htmlspecialchars($edit_club['master_in_charge']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Venue</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. Grounds" value="<?php echo htmlspecialchars($edit_club['venue']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Contact Email</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. club@nsbm.ac.lk" value="<?php echo htmlspecialchars($edit_club['email'] ?? ''); ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn <?php echo $edit_mode ? 'btn-success' : 'btn-primary'; ?> fw-bold py-2">
                                <?php echo $edit_mode ? 'Save Changes' : 'Add Club'; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="manage_clubs.php" class="btn btn-outline-secondary btn-sm">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side: Existing Clubs Table with Sequential Counters -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold mb-3">Existing Sports Clubs</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Club Name</th>
                                    <th>Master-in-Charge</th>
                                    <th>Venue</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT * FROM clubs ORDER BY id DESC");
                                if ($result && $result->num_rows > 0) {
                                    $counter = 1;
                                    while($row = $result->fetch_assoc()) {
                                        echo '<tr>
                                                <td>' . $counter++ . '</td>
                                                <td class="fw-bold">' . htmlspecialchars($row['club_name']) . '</td>
                                                <td>' . htmlspecialchars($row['master_in_charge']) . '</td>
                                                <td>' . htmlspecialchars($row['venue']) . '</td>
                                                <td class="text-center">
                                                    <a href="manage_clubs.php?edit=' . $row['id'] . '" class="btn btn-outline-primary btn-sm me-1">Edit</a>
                                                    ' . post_btn('manage_clubs.php', ['delete'=>$row['id']], 'Delete', 'btn btn-outline-danger btn-sm', 'Are you sure you want to delete this club?') . '
                                                </td>
                                             </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-4">No sports clubs added yet. Use the form on the left to add one!</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../validation.js"></script>
</body>
</html>