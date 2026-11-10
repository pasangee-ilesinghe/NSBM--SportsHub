<?php
session_start();

// Strict Admin Role Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="dashboard.php"><img src="../images/nsbm-logo.png" class="brand-logo" alt="NSBM"> SportsHub | Admin Control Panel</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-lg-end align-items-lg-center" id="navMenu">
            <span class="text-white me-3 small">Logged in as: <strong>admin</strong></span>
            <a href="../index.php" target="_blank" class="btn btn-outline-light btn-sm me-2">View Live Site</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="fw-bold mb-4 text-dark">Dashboard Management</h2>

        <div class="row g-4">
            <!-- 1. Manage Clubs -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold text-primary mb-2">Manage Clubs</h5>
                    <p class="text-muted small mb-4">Add new sports clubs, update descriptions, or remove outdated clubs.</p>
                    <a href="manage_clubs.php" class="btn btn-primary btn-sm mt-auto w-100 fw-bold">Manage Clubs</a>
                </div>
            </div>

            <!-- Manage Club Memberships Card -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold text-primary mb-2">Club Memberships</h5>
                    <p class="text-muted small mb-4">Review, approve, or reject student membership requests for sports clubs.</p>
                    <a href="manage_club_memberships.php" class="btn btn-primary btn-sm mt-auto w-100 fw-bold">Manage Members</a>
                </div>
            </div>

            <!-- 2. Match Results -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold text-success mb-2">Match Results & Upcoming Matches</h5>
                    <p class="text-muted small mb-4">Record and update recent match scores and outcomes for individual clubs.</p>
                    <a href="add_result.php" class="btn btn-success btn-sm mt-auto w-100 fw-bold">Add Result</a>
                </div>
            </div>

            <!-- 3. Facility Bookings -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold text-warning mb-2">Facility Bookings</h5>
                    <p class="text-muted small mb-4">Review, approve, or reject student requests for campus sports facilities.</p>
                    <a href="manage_bookings.php" class="btn btn-warning btn-sm mt-auto w-100 fw-bold text-dark">Manage Bookings</a>
                </div>
            </div>

            <!-- 4. Training Schedules -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold text-info mb-2">Training Sessions</h5>
                    <p class="text-muted small mb-4">Schedule upcoming special training sessions, and recurring practice times.</p>
                    <a href="schedule_sessions.php" class="btn btn-info btn-sm mt-auto w-100 fw-bold text-white">Schedule Training Sessions</a>
                </div>
            </div>

            <!-- 5. Tournaments -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold text-danger mb-2">Tournaments</h5>
                    <p class="text-muted small mb-4">Create and manage inter-faculty or campus-wide sports tournaments.</p>
                    <a href="manage_tournaments.php" class="btn btn-danger btn-sm mt-auto w-100 fw-bold">Manage Tournaments</a>
                </div>
            </div>

            <!-- 6. Reports & Analytics -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100 bg-white">
                    <h5 class="fw-bold text-secondary mb-2">Reports</h5>
                    <p class="text-muted small mb-4">Generate participation summaries, booking logs, and performance reports.</p>
                    <a href="reports.php" class="btn btn-secondary btn-sm mt-auto w-100 fw-bold">View Reports</a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>