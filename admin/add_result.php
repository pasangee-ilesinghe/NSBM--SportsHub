<?php
session_start();

// Include the centralized role security check to prevent student URL bypass
require_once 'auth_check.php';

include '../db.php';
require_once '../csrf.php';
csrf_protect();
$message = "";

$edit_mode = false;
$edit_match = [
    'id' => '',
    'match_date' => '',
    'sport' => '',
    'opponent' => '',
    'score' => '',
    'status' => 'Upcoming',
    'venue' => '',
    'outcome' => ''
];

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM matches WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_match = $res->fetch_assoc();
        $edit_mode = true;
    }
}

// Handle Add Match
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_match'])) {
    $match_date = trim($_POST['match_date']);
    $sport = trim($_POST['sport']);
    $opponent = trim($_POST['opponent']);
    $score = trim($_POST['score']);
    $status = trim($_POST['status']);
    $venue = trim($_POST['venue']);
    $outcome = trim($_POST['outcome']);

    $stmt = $conn->prepare("INSERT INTO matches (match_date, sport, opponent, score, status, venue, outcome) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $match_date, $sport, $opponent, $score, $status, $venue, $outcome);
    if ($stmt->execute()) {
        $message = "Match added successfully!";
    } else {
        $message = "Error adding match.";
    }
}

// Handle Update Match
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_match'])) {
    $id = intval($_POST['match_id']);
    $match_date = trim($_POST['match_date']);
    $sport = trim($_POST['sport']);
    $opponent = trim($_POST['opponent']);
    $score = trim($_POST['score']);
    $status = trim($_POST['status']);
    $venue = trim($_POST['venue']);
    $outcome = trim($_POST['outcome']);

    $stmt = $conn->prepare("UPDATE matches SET match_date = ?, sport = ?, opponent = ?, score = ?, status = ?, venue = ?, outcome = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $match_date, $sport, $opponent, $score, $status, $venue, $outcome, $id);
    if ($stmt->execute()) {
        header("Location: add_result.php?updated=1");
        exit();
    } else {
        $message = "Error updating match.";
        $edit_mode = true;
        $edit_match = ['id' => $id, 'match_date' => $match_date, 'sport' => $sport, 'opponent' => $opponent, 'score' => $score, 'status' => $status, 'venue' => $venue, 'outcome' => $outcome];
    }
}

if (isset($_GET['updated'])) {
    $message = "Match updated successfully!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['delete']);
    $delStmt = $conn->prepare("DELETE FROM matches WHERE id = ?");
    $delStmt->bind_param("i", $id);
    $delStmt->execute();
    header("Location: add_result.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Matches & Results - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Manage Matches & Results</span>
    </nav>

    <div class="container-fluid my-4 px-4">
        <?php if($message): ?>
            <div class="alert alert-success py-2 text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: Add / Edit Match Form -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white sticky-top" style="top: 20px;">
                    <h4 class="fw-bold text-primary mb-3"><?php echo $edit_mode ? 'Edit Match' : 'Add Match / Result'; ?></h4>
                    <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="update_match" value="1">
                            <input type="hidden" name="match_id" value="<?php echo $edit_match['id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="add_match" value="1">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Match Date</label>
                            <input type="date" name="match_date" class="form-control" value="<?php echo htmlspecialchars($edit_match['match_date'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Sport</label>
                            <input type="text" name="sport" class="form-control" placeholder="e.g. Cricket" value="<?php echo htmlspecialchars($edit_match['sport'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Opponent</label>
                            <input type="text" name="opponent" class="form-control" placeholder="e.g. CINEC Campus" value="<?php echo htmlspecialchars($edit_match['opponent'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Score (if completed)</label>
                            <input type="text" name="score" class="form-control" placeholder="e.g. 223/3 (46.2)" value="<?php echo htmlspecialchars($edit_match['score'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Upcoming" <?php echo (($edit_match['status'] ?? '') == 'Upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                                <option value="Completed" <?php echo (($edit_match['status'] ?? '') == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Venue</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. NSBM Playground" value="<?php echo htmlspecialchars($edit_match['venue'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Outcome</label>
                            <input type="text" name="outcome" class="form-control" placeholder="e.g. Win / Gold Medal / Pending" value="<?php echo htmlspecialchars($edit_match['outcome'] ?? ''); ?>">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn <?php echo $edit_mode ? 'btn-success' : 'btn-primary'; ?> fw-bold py-2">
                                <?php echo $edit_mode ? 'Save Changes' : 'Add Match'; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="add_result.php" class="btn btn-outline-secondary btn-sm">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Separated Cards -->
            <div class="col-lg-8">
                <!-- Card 1: Upcoming Matches -->
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
                    <h4 class="fw-bold text-primary mb-3">📅 Upcoming Matches</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Sport</th>
                                    <th>Opponent</th>
                                    <th>Venue</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $up_res = $conn->query("SELECT * FROM matches WHERE status = 'Upcoming' ORDER BY match_date ASC");
                                if ($up_res && $up_res->num_rows > 0) {
                                    while($row = $up_res->fetch_assoc()) {
                                        echo '<tr>
                                                <td>' . htmlspecialchars($row['match_date']) . '</td>
                                                <td class="fw-bold">' . htmlspecialchars($row['sport']) . '</td>
                                                <td>' . htmlspecialchars($row['opponent']) . '</td>
                                                <td>' . htmlspecialchars($row['venue']) . '</td>
                                                <td class="text-center">
                                                    <a href="add_result.php?edit=' . $row['id'] . '" class="btn btn-outline-primary btn-sm mb-1">Edit</a>
                                                    ' . post_btn('add_result.php', ['delete'=>$row['id']], 'Delete', 'btn btn-outline-danger btn-sm mb-1', 'Delete match record?') . '
                                                </td>
                                             </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-3">No upcoming matches scheduled.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card 2: Completed Matches & Results -->
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-success mb-3">🏆 Completed Matches & Results</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.92rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Sport</th>
                                    <th>Opponent</th>
                                    <th>Score</th>
                                    <th>Venue</th>
                                    <th>Outcome</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $comp_res = $conn->query("SELECT * FROM matches WHERE status = 'Completed' ORDER BY match_date DESC");
                                if ($comp_res && $comp_res->num_rows > 0) {
                                    while($row = $comp_res->fetch_assoc()) {
                                        echo '<tr>
                                                <td>' . htmlspecialchars($row['match_date']) . '</td>
                                                <td class="fw-bold">' . htmlspecialchars($row['sport']) . '</td>
                                                <td>' . htmlspecialchars($row['opponent']) . '</td>
                                                <td>' . (!empty($row['score']) ? htmlspecialchars($row['score']) : '<span class="text-muted">-</span>') . '</td>
                                                <td>' . htmlspecialchars($row['venue']) . '</td>
                                                <td>' . (!empty($row['outcome']) ? '<span class="badge bg-success">' . htmlspecialchars($row['outcome']) . '</span>' : '<span class="text-muted">-</span>') . '</td>
                                                <td class="text-center">
                                                    <a href="add_result.php?edit=' . $row['id'] . '" class="btn btn-outline-primary btn-sm mb-1">Edit</a>
                                                    ' . post_btn('add_result.php', ['delete'=>$row['id']], 'Delete', 'btn btn-outline-danger btn-sm mb-1', 'Delete match record?') . '
                                                </td>
                                             </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center text-muted py-3">No completed matches recorded yet.</td></tr>';
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