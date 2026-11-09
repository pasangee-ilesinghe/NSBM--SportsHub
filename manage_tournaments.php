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

$edit_mode = false;
$edit_tournament = [
    'id' => '',
    'tournament_name' => '',
    'sport' => '',
    'tournament_date' => '',
    'venue' => '',
    'description' => ''
];

// Check for edit trigger
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM tournaments WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_tournament = $res->fetch_assoc();
        $edit_mode = true;
    }
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_tournament'])) {
    $tournament_name = trim($_POST['tournament_name']);
    $sport = trim($_POST['sport']);
    $tournament_date = trim($_POST['tournament_date']);
    $venue = trim($_POST['venue']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO tournaments (tournament_name, sport, tournament_date, venue, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $tournament_name, $sport, $tournament_date, $venue, $description);
    if ($stmt->execute()) {
        $message = "Tournament added successfully!";
    } else {
        $message = "Error adding tournament.";
    }
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_tournament'])) {
    $id = intval($_POST['tournament_id']);
    $tournament_name = trim($_POST['tournament_name']);
    $sport = trim($_POST['sport']);
    $tournament_date = trim($_POST['tournament_date']);
    $venue = trim($_POST['venue']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare("UPDATE tournaments SET tournament_name = ?, sport = ?, tournament_date = ?, venue = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $tournament_name, $sport, $tournament_date, $venue, $description, $id);
    if ($stmt->execute()) {
        header("Location: manage_tournaments.php?updated=1");
        exit();
    } else {
        $message = "Error updating tournament.";
        $edit_mode = true;
        $edit_tournament = ['id' => $id, 'tournament_name' => $tournament_name, 'sport' => $sport, 'tournament_date' => $tournament_date, 'venue' => $venue, 'description' => $description];
    }
}

if (isset($_GET['updated'])) {
    $message = "Tournament updated successfully!";
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['delete']);
    $delStmt = $conn->prepare("DELETE FROM tournaments WHERE id = ?");
    $delStmt->bind_param("i", $id);
    $delStmt->execute();
    header("Location: manage_tournaments.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Tournaments - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Manage Tournaments</span>
    </nav>

    <div class="container my-5">
        <?php if($message): ?>
            <div class="alert alert-success py-2 text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-primary mb-3"><?php echo $edit_mode ? 'Edit Tournament' : 'Add Tournament'; ?></h4>
                    <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="update_tournament" value="1">
                            <input type="hidden" name="tournament_id" value="<?php echo $edit_tournament['id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="add_tournament" value="1">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Tournament Name</label>
                            <input type="text" name="tournament_name" class="form-control" value="<?php echo htmlspecialchars($edit_tournament['tournament_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Sport</label>
                            <input type="text" name="sport" class="form-control" value="<?php echo htmlspecialchars($edit_tournament['sport'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Date</label>
                            <input type="date" name="tournament_date" class="form-control" value="<?php echo htmlspecialchars($edit_tournament['tournament_date'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Venue</label>
                            <input type="text" name="venue" class="form-control" value="<?php echo htmlspecialchars($edit_tournament['venue'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Description</label>
                            <textarea name="description" class="form-control" rows="2" required><?php echo htmlspecialchars($edit_tournament['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn <?php echo $edit_mode ? 'btn-success' : 'btn-primary'; ?> fw-bold py-2">
                                <?php echo $edit_mode ? 'Save Changes' : 'Add Tournament'; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="manage_tournaments.php" class="btn btn-outline-secondary btn-sm">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold mb-4">Existing Tournaments</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 ps-3">Tournament Name</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Venue</th>
                                    <th class="py-3 text-center">Signups</th>
                                    <th class="py-3 text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT * FROM tournaments ORDER BY id DESC");
                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $tId = intval($row['id']);

                                        // Count using the exact tournament_id foreign key match
                                        $countQuery = "SELECT COUNT(*) as cnt FROM tournament_registrations WHERE tournament_id = $tId";
                                        $countRes = $conn->query($countQuery);
                                        $signupCount = ($countRes) ? $countRes->fetch_assoc()['cnt'] : 0;

                                        echo '<tr>
                                                <td class="py-3 ps-3">
                                                    <span class="fw-bold text-dark d-block">' . htmlspecialchars($row['tournament_name']) . '</span>
                                                    <span class="badge bg-secondary mt-1">' . htmlspecialchars($row['sport'] ?? '-') . '</span>
                                                </td>
                                                <td class="py-3 text-nowrap">' . htmlspecialchars($row['tournament_date']) . '</td>
                                                <td class="py-3">' . htmlspecialchars($row['venue'] ?? '-') . '</td>
                                                <td class="py-3 text-center">
                                                    <span class="badge bg-danger rounded-pill px-2 py-1">' . $signupCount . '</span>
                                                </td>
                                                <td class="py-3 text-end pe-3 text-nowrap">
                                                    <div class="d-inline-flex gap-1">
                                                        <a href="manage_tournaments.php?edit=' . $row['id'] . '" class="btn btn-outline-primary btn-sm px-3">Edit</a>
                                                        ' . post_btn('manage_tournaments.php', ['delete'=>$row['id']], 'Delete', 'btn btn-outline-danger btn-sm px-3', 'Delete tournament?') . '
                                                    </div>
                                                </td>
                                             </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-4">No tournaments found.</td></tr>';
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