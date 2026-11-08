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

// Handle Approve / Reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id']);
    
    if ($action == 'approve') {
        $updateStmt = $conn->prepare("UPDATE club_memberships SET status = 'Approved' WHERE id = ?");
        $updateStmt->bind_param("i", $id);
        $updateStmt->execute();
    } elseif ($action == 'reject' || $action == 'delete') {
        $deleteStmt = $conn->prepare("DELETE FROM club_memberships WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        $deleteStmt->execute();
    }
    
    header("Location: manage_club_memberships.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Club Memberships - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <!-- Admin Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Manage Student Club Membership Requests</span>
    </nav>

    <div class="container my-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h3 class="fw-bold mb-3">Club Membership Requests</h3>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Student Name</th>
                            <th>Club Requested</th>
                            <th>Joined/Requested Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch memberships joined with clubs table to get the club name
                        $query = "SELECT cm.id, cm.student_name, cm.joined_date, cm.status, c.club_name 
                                  FROM club_memberships cm 
                                  JOIN clubs c ON cm.club_id = c.id 
                                  ORDER BY cm.id DESC";
                        $result = $conn->query($query);

                        if ($result && $result->num_rows > 0) {
                            $row_no = $result->num_rows;   // countdown: newest request shows the highest number, down to 1
                            while($row = $result->fetch_assoc()) {
                                $display_no = $row_no--;
                                $badgeColor = 'bg-warning text-dark';
                                if($row['status'] == 'Approved') $badgeColor = 'bg-success';
                                if($row['status'] == 'Rejected') $badgeColor = 'bg-danger';

                                echo '<tr>
                                        <td>' . $display_no . '</td>
                                        <td class="fw-bold">' . htmlspecialchars($row['student_name']) . '</td>
                                        <td class="text-primary fw-semibold">' . htmlspecialchars($row['club_name']) . '</td>
                                        <td>' . htmlspecialchars($row['joined_date']) . '</td>
                                        <td><span class="badge ' . $badgeColor . '">' . $row['status'] . '</span></td>
                                        <td class="text-center">
                                            ' . post_btn('manage_club_memberships.php', ['action'=>'approve','id'=>$row['id']], 'Approve', 'btn btn-success btn-sm px-2 me-1', '') . '
                                            ' . post_btn('manage_club_memberships.php', ['action'=>'reject','id'=>$row['id']], 'Reject', 'btn btn-danger btn-sm px-2', 'Reject/Remove this membership request?') . '
                                        </td>
                                     </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-muted py-4">No membership requests found yet.</td></tr>';
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