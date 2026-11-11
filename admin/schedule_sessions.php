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

// Fetch all available clubs for dropdowns
$clubsQuery = $conn->query("SELECT club_name FROM clubs ORDER BY club_name ASC");

$edit_mode = false;
$edit_type = ''; // 'recurring' or 'special'
$edit_data = [];

// Handle Edit Trigger
if (isset($_GET['edit_recurring'])) {
    $id = intval($_GET['edit_recurring']);
    $stmt = $conn->prepare("SELECT * FROM club_schedules WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
        $edit_mode = true;
        $edit_type = 'recurring';
    }
} elseif (isset($_GET['edit_special'])) {
    $id = intval($_GET['edit_special']);
    $stmt = $conn->prepare("SELECT * FROM club_special_sessions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
        $edit_mode = true;
        $edit_type = 'special';
    }
}

// 1. Handle Add Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_session'])) {
    $club_name = trim($_POST['club_name']);
    $session_type = trim($_POST['session_type']);
    $time = trim($_POST['time']);
    $venue = trim($_POST['venue']);

    if ($session_type == 'special' && trim($_POST['session_date'] ?? '') === '') {
        $message = "Please choose a date for the special session.";
    } else {
        if ($session_type == 'recurring') {
            $session_day = trim($_POST['session_day']);
            $stmt = $conn->prepare("INSERT INTO club_schedules (club_name, session_day, time, venue) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $club_name, $session_day, $time, $venue);
        } else {
            $session_date = trim($_POST['session_date']);
            $description = trim($_POST['description'] ?? 'Special Session');
            $stmt = $conn->prepare("INSERT INTO club_special_sessions (club_name, session_date, time, venue, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $club_name, $session_date, $time, $venue, $description);
        }

        if ($stmt->execute()) {
            $message = "Session added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// 2. Handle Update Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_session'])) {
    $id = intval($_POST['session_id']);
    $session_type = trim($_POST['session_type']);
    $club_name = trim($_POST['club_name']);
    $time = trim($_POST['time']);
    $venue = trim($_POST['venue']);

    if ($session_type == 'recurring') {
        $session_day = trim($_POST['session_day']);
        $stmt = $conn->prepare("UPDATE club_schedules SET club_name = ?, session_day = ?, time = ?, venue = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $club_name, $session_day, $time, $venue, $id);
    } else {
        $session_date = trim($_POST['session_date']);
        $description = trim($_POST['description']);
        $stmt = $conn->prepare("UPDATE club_special_sessions SET club_name = ?, session_date = ?, time = ?, venue = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $club_name, $session_date, $time, $venue, $description, $id);
    }

    if ($stmt->execute()) {
        header("Location: schedule_sessions.php?updated=1");
        exit();
    } else {
        $message = "Error updating session.";
    }
}

if (isset($_GET['updated'])) {
    $message = "Session updated successfully!";
}

// 3. Handle Deletes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_recurring'])) {
    $id = intval($_POST['delete_recurring']);
    $conn->query("DELETE FROM club_schedules WHERE id = $id");
    header("Location: schedule_sessions.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_special'])) {
    $id = intval($_POST['delete_special']);
    $conn->query("DELETE FROM club_special_sessions WHERE id = $id");
    header("Location: schedule_sessions.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule Sessions - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <script>
        function toggleSessionFields() {
            let type = document.getElementById('session_type').value;
            if (type === 'recurring') {
                document.getElementById('recurring_div').style.display = 'block';
                document.getElementById('special_div').style.display = 'none';
            } else {
                document.getElementById('recurring_div').style.display = 'none';
                document.getElementById('special_div').style.display = 'block';
            }
        }
    </script>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Manage Club Training & Sessions</span>
    </nav>

    <div class="container-fluid my-4 px-4">
        <?php if($message): ?>
            <div class="alert alert-success py-2 text-center mb-4"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: Add / Edit Form -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-primary mb-3">
                        <?php echo $edit_mode ? 'Edit ' . ucfirst($edit_type) . ' Session' : 'Add Club Session'; ?>
                    </h4>

                    <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="update_session" value="1">
                            <input type="hidden" name="session_id" value="<?php echo $edit_data['id']; ?>">
                            <input type="hidden" name="session_type" value="<?php echo $edit_type; ?>">
                        <?php else: ?>
                            <input type="hidden" name="add_session" value="1">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Select Club / Sport</label>
                            <select name="club_name" class="form-select" required>
                                <option value="">-- Choose Club --</option>
                                <?php 
                                $clubsData = $conn->query("SELECT club_name FROM clubs ORDER BY club_name ASC");
                                if ($clubsData && $clubsData->num_rows > 0) {
                                    while($c = $clubsData->fetch_assoc()) {
                                        $selected = ($edit_mode && $edit_data['club_name'] == $c['club_name']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($c['club_name']) . '" ' . $selected . '>' . htmlspecialchars($c['club_name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <?php if (!$edit_mode): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-semibold">Session Category</label>
                                <select name="session_type" id="session_type" class="form-select" onchange="toggleSessionFields()" required>
                                    <option value="recurring">Weekly Recurring Schedule</option>
                                    <option value="special">Special Session (Specific Date)</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Recurring Field -->
                        <div class="mb-3" id="recurring_div" <?php echo ($edit_mode && $edit_type == 'special') ? 'style="display:none;"' : ''; ?>>
                            <label class="form-label text-muted small fw-semibold">Day of the Week</label>
                            <select name="session_day" class="form-select">
                                <?php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                foreach ($days as $day) {
                                    $selected = ($edit_mode && $edit_type == 'recurring' && $edit_data['session_day'] == $day) ? 'selected' : '';
                                    echo "<option value=\"$day\" $selected>$day</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Special Session Fields -->
                        <div id="special_div" <?php echo (!$edit_mode || $edit_type == 'recurring') ? 'style="display:none;"' : ''; ?>>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-semibold">Session Date</label>
                                <input type="date" name="session_date" class="form-control" value="<?php echo htmlspecialchars($edit_data['session_date'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-semibold">Session Title / Description</label>
                                <input type="text" name="description" class="form-control" placeholder="e.g., Guest Workshop" value="<?php echo htmlspecialchars($edit_data['description'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Time</label>
                            <input type="text" name="time" class="form-control" placeholder="e.g., 3.00 p.m. - 5.00 p.m." value="<?php echo htmlspecialchars($edit_data['time'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">Venue</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g., Indoor Stadium" value="<?php echo htmlspecialchars($edit_data['venue'] ?? ''); ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn <?php echo $edit_mode ? 'btn-success' : 'btn-primary'; ?> fw-bold py-2 shadow-sm">
                                <?php echo $edit_mode ? 'Save Changes' : 'Save Session Schedule'; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="schedule_sessions.php" class="btn btn-outline-secondary btn-sm">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Tables for Recurring & Special Sessions -->
            <div class="col-lg-8">
                <!-- Weekly Recurring Table -->
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
                    <h4 class="fw-bold mb-3 text-dark">Weekly Recurring Schedules</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Club</th>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Venue</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rec_res = $conn->query("SELECT * FROM club_schedules ORDER BY FIELD(session_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
                                if ($rec_res && $rec_res->num_rows > 0) {
                                    while($row = $rec_res->fetch_assoc()) {
                                        echo '<tr>
                                                <td class="fw-bold">' . htmlspecialchars($row['club_name']) . '</td>
                                                <td><span class="badge bg-primary">' . htmlspecialchars($row['session_day']) . '</span></td>
                                                <td>' . htmlspecialchars($row['time']) . '</td>
                                                <td>' . htmlspecialchars($row['venue']) . '</td>
                                                <td class="text-center">
                                                    <a href="schedule_sessions.php?edit_recurring=' . $row['id'] . '" class="btn btn-outline-primary btn-sm me-1">Edit</a>
                                                    ' . post_btn('schedule_sessions.php', ['delete_recurring'=>$row['id']], 'Delete', 'btn btn-outline-danger btn-sm', 'Delete recurring schedule?') . '
                                                </td>
                                             </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-3">No recurring schedules added yet.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Special Sessions Table -->
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold mb-3 text-dark">Special Sessions</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Club</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Time / Venue</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $spec_res = $conn->query("SELECT * FROM club_special_sessions ORDER BY session_date DESC");
                                if ($spec_res && $spec_res->num_rows > 0) {
                                    while($row = $spec_res->fetch_assoc()) {
                                        echo '<tr>
                                                <td class="fw-bold">' . htmlspecialchars($row['club_name']) . '</td>
                                                <td>' . htmlspecialchars($row['description']) . '</td>
                                                <td>' . htmlspecialchars($row['session_date']) . '</td>
                                                <td><small class="text-muted">' . htmlspecialchars($row['time']) . '<br>' . htmlspecialchars($row['venue']) . '</small></td>
                                                <td class="text-center">
                                                    <a href="schedule_sessions.php?edit_special=' . $row['id'] . '" class="btn btn-outline-primary btn-sm me-1">Edit</a>
                                                    ' . post_btn('schedule_sessions.php', ['delete_special'=>$row['id']], 'Delete', 'btn btn-outline-danger btn-sm', 'Delete special session?') . '
                                                </td>
                                             </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-3">No special sessions scheduled.</td></tr>';
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