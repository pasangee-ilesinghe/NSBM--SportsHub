<?php
session_start();

// Strict Admin Role Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
require_once '../booking_cleanup.php';
cleanup_expired_bookings($conn);

// Handle Date/Time Filter Range if selected
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$dateCondition = "";

if ($filter === 'month') {
    $dateCondition = " WHERE created_at >= NOW() - INTERVAL 1 MONTH";
} elseif ($filter === 'semester') {
    $dateCondition = " WHERE created_at >= NOW() - INTERVAL 6 MONTH";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>System Reports - Admin Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <!-- Admin Navbar -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm justify-content-between">
        <a class="navbar-brand fw-bold" href="dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Participation Summaries & Logs</span>
    </nav>

    <div class="container my-5">
        
        <!-- Header Actions & Filter Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-secondary mb-1">System Analytics & Summary Reports</h2>
                <p class="text-muted mb-0">Overview of platform engagement across NSBM SportsHub.</p>
            </div>
            <div class="d-flex gap-2">
                <!-- Date Filter Form -->
                <form method="GET" class="d-flex align-items-center gap-2">
                    <select name="filter" class="form-select form-select-sm px-3 py-2 filter-dropdown" onchange="this.form.submit()">
                        <option value="all" <?php if($filter=='all') echo 'selected'; ?>>All Time</option>
                        <option value="semester" <?php if($filter=='semester') echo 'selected'; ?>>Past 6 Months</option>
                        <option value="month" <?php if($filter=='month') echo 'selected'; ?>>Past 30 Days</option>
                    </select>
                </form>
                <!-- Export to pdf Button -->
                <a href="export_pdf.php" class="btn btn-success btn-sm d-flex align-items-center gap-1 px-3">
                    Export PDF Report
                </a>
            </div>
        </div>

        <!-- Top Overall Summary Cards -->
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <div class="row text-center g-3">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <h6 class="text-muted">Total Club Memberships</h6>
                        <h2 class="fw-bold text-primary mb-0">
                            <?php 
                            $res = $conn->query("SELECT COUNT(*) as count FROM club_memberships");
                            echo ($res) ? $res->fetch_assoc()['count'] : 0; 
                            ?>
                        </h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <h6 class="text-muted">Total Facility Bookings</h6>
                        <h2 class="fw-bold text-success mb-0">
                            <?php 
                            $res = $conn->query("SELECT COUNT(*) as count FROM facility_bookings");
                            echo ($res) ? $res->fetch_assoc()['count'] : 0; 
                            ?>
                        </h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <h6 class="text-muted">Tournament Signups</h6>
                        <h2 class="fw-bold text-danger mb-0">
                            <?php 
                            $res = $conn->query("SELECT COUNT(*) as count FROM tournament_registrations");
                            echo ($res) ? $res->fetch_assoc()['count'] : 0; 
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facility Bookings Status Breakdown -->
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <h4 class="fw-bold text-dark mb-3">Facility Bookings Status Breakdown</h4>
            <div class="row text-center g-3">
                <div class="col-md-4">
                    <div class="p-3 border border-warning rounded bg-white shadow-sm">
                        <h6 class="text-warning fw-bold">Pending Requests</h6>
                        <h3 class="fw-bold text-dark mb-0">
                            <?php 
                            $pendingRes = $conn->query("SELECT COUNT(*) as count FROM facility_bookings WHERE status = 'Pending'");
                            echo ($pendingRes) ? $pendingRes->fetch_assoc()['count'] : 0; 
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border border-success rounded bg-white shadow-sm">
                        <h6 class="text-success fw-bold">Approved Requests</h6>
                        <h3 class="fw-bold text-dark mb-0">
                            <?php 
                            $approvedRes = $conn->query("SELECT COUNT(*) as count FROM facility_bookings WHERE status = 'Approved'");
                            echo ($approvedRes) ? $approvedRes->fetch_assoc()['count'] : 0; 
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border border-danger rounded bg-white shadow-sm">
                        <h6 class="text-danger fw-bold">Rejected Requests</h6>
                        <h3 class="fw-bold text-dark mb-0">
                            <?php 
                            $rejectedRes = $conn->query("SELECT COUNT(*) as count FROM facility_bookings WHERE status = 'Rejected'");
                            echo ($rejectedRes) ? $rejectedRes->fetch_assoc()['count'] : 0; 
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Match Performance Overview Card -->
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <h4 class="fw-bold text-dark mb-3">Match Performance Overview</h4>
            <div class="row text-center g-3">
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <h6 class="text-secondary fw-semibold">Total Matches Played</h6>
                        <h3 class="fw-bold text-dark mb-0">
                            <?php 
                            $totalMatches = 0;
                            $matchCheck = $conn->query("SHOW TABLES LIKE 'matches'");
                            if ($matchCheck && $matchCheck->num_rows > 0) {
                                $mRes = $conn->query("SELECT COUNT(*) as count FROM matches WHERE status = 'Completed'");
                                if ($mRes) { $totalMatches = $mRes->fetch_assoc()['count']; }
                            }
                            echo $totalMatches;
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border border-success rounded bg-white shadow-sm">
                        <h6 class="text-success fw-bold">Total Wins</h6>
                        <h3 class="fw-bold text-success mb-0">
                            <?php 
                            $totalWins = 0;
                            if ($matchCheck && $matchCheck->num_rows > 0) {
                                // Counts standard 'Win' outcomes plus any medals/trophies as wins
                                $winRes = $conn->query("SELECT COUNT(*) as count FROM matches WHERE outcome = 'Win' OR outcome LIKE '%Medal%' OR outcome LIKE '%Trophy%' OR outcome LIKE '%Gold%'");
                                if ($winRes) { $totalWins = $winRes->fetch_assoc()['count']; }
                            }
                            echo $totalWins;
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border border-danger rounded bg-white shadow-sm">
                        <h6 class="text-danger fw-bold">Total Losses</h6>
                        <h3 class="fw-bold text-danger mb-0">
                            <?php 
                            $totalLosses = 0;
                            if ($matchCheck && $matchCheck->num_rows > 0) {
                                $lossRes = $conn->query("SELECT COUNT(*) as count FROM matches WHERE outcome = 'Loss'");
                                if ($lossRes) { $totalLosses = $lossRes->fetch_assoc()['count']; }
                            }
                            echo $totalLosses;
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border border-warning rounded bg-white shadow-sm">
                        <h6 class="text-warning fw-bold">Medals / Trophies Won</h6>
                        <h3 class="fw-bold text-dark mb-0">
                            <?php 
                            $totalMedals = 0;
                            if ($matchCheck && $matchCheck->num_rows > 0) {
                                $medRes = $conn->query("SELECT COUNT(*) as count FROM matches WHERE outcome LIKE '%Medal%' OR outcome LIKE '%Trophy%' OR outcome LIKE '%Gold%'");
                                if ($medRes) { $totalMedals = $medRes->fetch_assoc()['count']; }
                            }
                            echo $totalMedals;
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tournament Performance & Participation Breakdown -->
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <h4 class="fw-bold text-dark mb-3">Tournament Participation Overview</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-3">Tournament Name</th>
                            <th class="py-3">Sport Category</th>
                            <th class="py-3">Date / Schedule</th>
                            <th class="py-3 text-center">Registered Teams / Participants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tournamentQuery = "SELECT t.id, t.tournament_name, t.sport, t.tournament_date, COUNT(tr.id) as reg_count 
                                            FROM tournaments t 
                                            LEFT JOIN tournament_registrations tr ON t.id = tr.tournament_id 
                                            GROUP BY t.id, t.tournament_name, t.sport, t.tournament_date 
                                            ORDER BY reg_count DESC";
                        
                        $tournamentResult = $conn->query($tournamentQuery);

                        if ($tournamentResult && $tournamentResult->num_rows > 0) {
                            while($tRow = $tournamentResult->fetch_assoc()) {
                                echo '<tr>
                                        <td class="py-3 ps-3 fw-bold text-dark">' . htmlspecialchars($tRow['tournament_name']) . '</td>
                                        <td class="py-3"><span class="badge bg-secondary">' . htmlspecialchars($tRow['sport'] ?? '-') . '</span></td>
                                        <td class="py-3 text-nowrap">' . htmlspecialchars($tRow['tournament_date']) . '</td>
                                        <td class="py-3 text-center"><span class="badge bg-danger fs-6 px-3 py-2 rounded-pill">' . $tRow['reg_count'] . '</span></td>
                                      </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center text-muted py-3">No tournaments found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Breakdown: Members per Sport Club -->
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h4 class="fw-bold text-dark mb-3">Members Breakdown per Sport Club</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-3">Club Name</th>
                            <th class="py-3">Venue</th>
                            <th class="py-3">Master-in-Charge</th>
                            <th class="py-3 text-center">Total Enrolled Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $clubReportQuery = "SELECT c.club_name, c.venue, c.master_in_charge, COUNT(cm.id) as member_count 
                                            FROM clubs c 
                                            LEFT JOIN club_memberships cm ON c.id = cm.club_id 
                                            GROUP BY c.id 
                                            ORDER BY member_count DESC";
                        
                        $clubReportResult = $conn->query($clubReportQuery);

                        if ($clubReportResult && $clubReportResult->num_rows > 0) {
                            while($row = $clubReportResult->fetch_assoc()) {
                                echo '<tr>
                                        <td class="py-3 ps-3 fw-bold text-primary">' . htmlspecialchars($row['club_name']) . '</td>
                                        <td class="py-3">' . htmlspecialchars($row['venue'] ?? '-') . '</td>
                                        <td class="py-3">' . htmlspecialchars($row['master_in_charge'] ?? '-') . '</td>
                                        <td class="py-3 text-center"><span class="badge bg-primary fs-6 px-3 py-2">' . $row['member_count'] . '</span></td>
                                      </tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center text-muted py-3">No clubs or member data found.</td></tr>';
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