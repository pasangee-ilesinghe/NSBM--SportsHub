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
require_once '../booking_cleanup.php';
ensure_admin_hidden_column($conn);
cleanup_expired_bookings($conn);   // removes expired Pending/Rejected requests

// Handle Approve, Reject, or Delete actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id']);
    
    if ($action == 'approve') {
        $updateStmt = $conn->prepare("UPDATE facility_bookings SET status = 'Approved' WHERE id = ?");
        $updateStmt->bind_param("i", $id);
        $updateStmt->execute();
    } elseif ($action == 'reject') {
        $updateStmt = $conn->prepare("UPDATE facility_bookings SET status = 'Rejected' WHERE id = ?");
        $updateStmt->bind_param("i", $id);
        $updateStmt->execute();
    } elseif ($action == 'delete') {
        // Only hides the request from the admin list - the student keeps it in their own booking history.
        // (To free up an approved slot, use Reject instead.)
        $hideStmt = $conn->prepare("UPDATE facility_bookings SET admin_hidden = 1 WHERE id = ?");
        $hideStmt->bind_param("i", $id);
        $hideStmt->execute();
    }
    
    header("Location: manage_bookings.php");
    exit();
}

// Which bookings to list: upcoming only (default) or everything
$today = date('Y-m-d');
$showPast = isset($_GET['past']) && $_GET['past'] === '1';
$pc = $conn->prepare("SELECT COUNT(*) AS c FROM facility_bookings WHERE booking_date < ? AND admin_hidden = 0");
$pc->bind_param("s", $today);
$pc->execute();
$pastCount = (int)$pc->get_result()->fetch_assoc()['c'];
$pc->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Facility Bookings - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <!-- Admin Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Manage Student Booking Requests</span>
    </nav>

    <div class="container my-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <h3 class="fw-bold mb-0">Student Bookings</h3>
                <?php if ($showPast): ?>
                    <a href="manage_bookings.php" class="btn btn-outline-primary btn-sm">Hide past bookings</a>
                <?php elseif ($pastCount > 0): ?>
                    <a href="manage_bookings.php?past=1" class="btn btn-outline-primary btn-sm">Show past bookings (<?php echo $pastCount; ?>)</a>
                <?php endif; ?>
            </div>
            <p class="text-muted small mb-3"><?php echo $showPast ? 'Showing all bookings, including past dates.' : 'Showing today and upcoming dates. Past requests that were never approved are removed automatically.'; ?></p>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Student Name</th>
                            <th>Facility Requested</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($showPast) {
                            $listStmt = $conn->prepare("SELECT * FROM facility_bookings WHERE admin_hidden = 0 ORDER BY id DESC");
                        } else {
                            $listStmt = $conn->prepare("SELECT * FROM facility_bookings WHERE booking_date >= ? AND admin_hidden = 0 ORDER BY id DESC");
                            $listStmt->bind_param("s", $today);
                        }
                        $listStmt->execute();
                        $result = $listStmt->get_result();

                        if ($result && $result->num_rows > 0) {
                            $row_no = 1;   // display-only row number (1, 2, 3 ...); the real id is still used by the buttons
                            while($row = $result->fetch_assoc()) {
                                $badgeColor = 'bg-warning text-dark';
                                if($row['status'] == 'Approved') $badgeColor = 'bg-success';
                                if($row['status'] == 'Rejected') $badgeColor = 'bg-danger';

                                echo '<tr>
                                        <td>' . $row_no++ . '</td>
                                        <td class="fw-bold">' . htmlspecialchars($row['student_name']) . '</td>
                                        <td>' . htmlspecialchars($row['facility_name']) . '</td>
                                        <td>' . htmlspecialchars($row['booking_date']) . '</td>
                                        <td><span class="badge ' . $badgeColor . '">' . $row['status'] . '</span></td>
                                        <td class="text-center">
                                            ' . post_btn('manage_bookings.php', ['action'=>'approve','id'=>$row['id']], 'Approve', 'btn btn-success btn-sm px-2 me-1', '') . '
                                            ' . post_btn('manage_bookings.php', ['action'=>'reject','id'=>$row['id']], 'Reject', 'btn btn-warning btn-sm px-2 me-1', '') . '
                                            ' . post_btn('manage_bookings.php', ['action'=>'delete','id'=>$row['id']], 'Delete', 'btn btn-danger btn-sm px-2', 'Remove this booking from the admin list? The student will still see it in their booking history. (To free up the slot, use Reject instead.)') . '
                                        </td>
                                     </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-muted py-4">' . ($showPast ? 'No bookings found.' : 'No upcoming booking requests.') . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
