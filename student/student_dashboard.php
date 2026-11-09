<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}
include '../db.php'; 
require_once '../csrf.php';
csrf_protect();
require_once '../booking_cleanup.php';
cleanup_expired_bookings($conn);
$student_name = $_SESSION['user'];
$student_id = $_SESSION['student_id']; // Relational student identifier

// Handle leaving a club (Secured by student_id)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave'])) {
    $membership_id = intval($_POST['leave']);
    $del = $conn->prepare("DELETE FROM club_memberships WHERE student_id = ? AND id = ?");
    $del->bind_param("si", $student_id, $membership_id);
    $del->execute();
    header("Location: student_dashboard.php");
    exit();
}

// Handle cancelling a facility booking (Secured by student_id)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = intval($_POST['cancel_booking']);
    $cancelStmt = $conn->prepare("DELETE FROM facility_bookings WHERE id = ? AND student_id = ?");
    $cancelStmt->bind_param("is", $booking_id, $student_id);
    $cancelStmt->execute();
    header("Location: student_dashboard.php");
    exit();
}

// Handle cancelling/withdrawing from a tournament registration (Secured by student_id)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_tournament'])) {
    $t_id = intval($_POST['cancel_tournament']);
    $tCancelStmt = $conn->prepare("DELETE FROM tournament_registrations WHERE id = ? AND student_id = ?");
    $tCancelStmt->bind_param("is", $t_id, $student_id);
    $tCancelStmt->execute();
    header("Location: student_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="../index.php"><img src="../images/nsbm-logo.png" class="brand-logo" alt="NSBM"> SportsHub</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-lg-end align-items-lg-center" id="navMenu">
            <span class="text-white me-2 small">Welcome, <strong><?php echo htmlspecialchars($student_name); ?></strong></span>
            <a href="../index.php" class="text-white text-decoration-none mx-1">Home</a>
            <a href="../clubs.php" class="text-white text-decoration-none mx-1">Browse Clubs</a>
            <a href="../book_facility.php" class="text-white text-decoration-none mx-1">Book Facility</a>
            <a href="../tournaments.php" class="text-white text-decoration-none mx-1">Tournaments</a>
            <a href="student_dashboard.php" class="text-white text-decoration-none mx-1 fw-bold">Dashboard</a>
            <a href="edit_profile.php" class="btn btn-outline-light btn-sm fw-semibold ms-2">⚙️ Profile</a>
            <a href="../logout.php" class="btn btn-light btn-sm text-primary fw-bold ms-2">Logout</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-dark section-title">Student Participation Dashboard</h2>
                <p class="text-muted small mb-0">Track your registered sports clubs, tournament participations, and facility reservations.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="edit_profile.php" class="btn btn-primary btn-sm fw-bold">Edit Profile</a>
                <a href="change_password.php" class="btn btn-outline-secondary btn-sm fw-bold">Change Password</a>
                <a href="../tournaments.php" class="btn btn-outline-primary btn-sm fw-bold">Explore Tournaments</a>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- 1. My Registered Sports Clubs -->
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-success mb-3">My Sports Club Memberships</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Club Name</th>
                                    <th>Venue</th>
                                    <th>Master-in-Charge</th>
                                    <th>Joined Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Relational query using student_id
                                $stmt = $conn->prepare("SELECT c.*, cm.id as membership_id, cm.joined_date, cm.status FROM club_memberships cm JOIN clubs c ON cm.club_id = c.id WHERE cm.student_id = ?");
                                $stmt->bind_param("s", $student_id);
                                $stmt->execute();
                                $clubRes = $stmt->get_result();

                                if ($clubRes && $clubRes->num_rows > 0) {
                                    $counter = 1;
                                    while($club = $clubRes->fetch_assoc()) {
                                        $badgeColor = 'bg-warning text-dark';
                                        if($club['status'] == 'Approved') $badgeColor = 'bg-success';
                                        if($club['status'] == 'Rejected') $badgeColor = 'bg-danger';
                                        $displayStatus = !empty($club['status']) ? $club['status'] : 'Pending';

                                        // Format date to show only YYYY-MM-DD
                                        $formattedJoinDate = !empty($club['joined_date']) ? date('Y-m-d', strtotime($club['joined_date'])) : '';

                                        echo '<tr>
                                                <td>' . $counter++ . '</td>
                                                <td class="fw-bold text-primary">' . htmlspecialchars($club['club_name']) . '</td>
                                                <td>' . htmlspecialchars($club['venue']) . '</td>
                                                <td>' . htmlspecialchars($club['master_in_charge']) . '</td>
                                                <td>' . htmlspecialchars($formattedJoinDate) . '</td>
                                                <td><span class="badge ' . $badgeColor . '">' . $displayStatus . '</span></td>
                                                <td>' . post_btn('student_dashboard.php', ['leave'=>$club['membership_id']], 'Leave Club', 'btn btn-outline-danger btn-sm fw-bold', 'Are you sure you want to leave this club?') . '</td>
                                              </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center text-muted py-3">You haven\'t joined any sports clubs yet. <a href="../clubs.php">Browse clubs here</a></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. Tournament Registrations History -->
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-dark mb-3">My Tournament Registrations</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Tournament Name</th>
                                    <th>Sport / Category</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Relational query using student_id
                                $tStmt = $conn->prepare("SELECT * FROM tournament_registrations WHERE student_id = ? ORDER BY id DESC");
                                $tStmt->bind_param("s", $student_id);
                                $tStmt->execute();
                                $tResult = $tStmt->get_result();

                                if ($tResult && $tResult->num_rows > 0) {
                                    $counter = 1;
                                    while($tRow = $tResult->fetch_assoc()) {
                                        // Format date to show only YYYY-MM-DD
                                        $formattedRegDate = !empty($tRow['registration_date']) ? date('Y-m-d', strtotime($tRow['registration_date'])) : '';

                                        echo '<tr>
                                                <td>' . $counter++ . '</td>
                                                <td class="fw-bold text-dark">' . htmlspecialchars($tRow['tournament_name']) . '</td>
                                                <td>' . htmlspecialchars($tRow['sport']) . ' <span class="badge bg-light text-dark border">' . htmlspecialchars($tRow['category']) . '</span></td>
                                                <td>' . htmlspecialchars($formattedRegDate) . '</td>
                                                <td><span class="badge bg-success">' . htmlspecialchars($tRow['status']) . '</span></td>
                                                <td>
                                                    ' . post_btn('student_dashboard.php', ['cancel_tournament'=>$tRow['id']], 'Withdraw', 'btn btn-outline-danger btn-sm fw-bold', 'Are you sure you want to withdraw from this tournament?') . '
                                                </td>
                                              </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center text-muted py-3">You haven\'t registered for any tournaments yet. <a href="../tournaments.php">Explore tournaments</a></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Facility Bookings History & Cancellation -->
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-primary mb-3">My Facility & Equipment Bookings</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Facility Requested</th>
                                    <th>Required Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Relational query using student_id
                                $bStmt = $conn->prepare("SELECT * FROM facility_bookings WHERE student_id = ? ORDER BY id DESC");
                                $bStmt->bind_param("s", $student_id);
                                $bStmt->execute();
                                $bResult = $bStmt->get_result();

                                if ($bResult && $bResult->num_rows > 0) {
                                    $counter = 1;
                                    while($row = $bResult->fetch_assoc()) {
                                        $badgeColor = 'bg-warning text-dark';
                                        if($row['status'] == 'Approved') $badgeColor = 'bg-success';
                                        if($row['status'] == 'Rejected') $badgeColor = 'bg-danger';

                                        echo '<tr>
                                                <td>' . $counter++ . '</td>
                                                <td class="fw-bold">' . htmlspecialchars($row['facility_name']) . '</td>
                                                <td>' . htmlspecialchars($row['booking_date']) . '</td>
                                                <td><span class="badge ' . $badgeColor . '">' . $row['status'] . '</span></td>
                                                <td>
                                                    ' . post_btn('student_dashboard.php', ['cancel_booking'=>$row['id']], 'Cancel', 'btn btn-outline-danger btn-sm fw-bold', 'Are you sure you want to cancel this booking request? This will free up the slot.') . '
                                                </td>
                                              </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="text-center text-muted py-3">You haven\'t booked any facilities yet. <a href="../book_facility.php">Book now</a></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Site footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-5">
                    <img src="../images/nsbm-logo.png" class="brand-logo mb-2" alt="NSBM Green University Town">
                    <p class="mb-0 small">Play, train and compete at NSBM Green University, Pitipana.</p>
                </div>
                <div class="col-md-4 small">
                    <a href="../clubs.php" class="me-3">Clubs</a>
                    <a href="../matches.php" class="me-3">Matches</a>
                    <a href="../tournaments.php" class="me-3">Tournaments</a>
                    <a href="../contact.php">Contact</a>
                </div>
                <div class="col-md-3 small">
                    <div>inquiries@nsbm.ac.lk</div>
                    <div>011 544 5000</div>
                </div>
            </div>
            <div class="copy text-center">&copy; 2026 NSBM SportsHub. All rights reserved.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>