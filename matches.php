<?php
session_start();
include 'db.php';

// Determine active tab (default to 'upcoming')
$tab = $_GET['tab'] ?? 'upcoming';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Matches and fixtures - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="bg-light">

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
        <a class="navbar-brand fw-bold" href="index.php"><img src="images/nsbm-logo.png" class="brand-logo" alt="NSBM"> SportsHub</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-lg-end align-items-lg-center" id="navMenu">
            <a href="index.php" class="text-white text-decoration-none mx-2">Home</a>
            <a href="clubs.php" class="text-white text-decoration-none mx-2">Clubs</a>
            <a href="matches.php" class="text-white text-decoration-none mx-2 fw-bold">Matches</a>
            <a href="tournaments.php" class="text-white text-decoration-none mx-2">Tournaments</a>
            <a href="contact.php" class="text-white text-decoration-none mx-2">Contact</a>

            <?php if (isset($_SESSION['user']) || isset($_SESSION['student_logged'])): ?>
                <a href="student/student_dashboard.php" class="text-white text-decoration-none mx-2 fw-bold">Dashboard</a>
                <a href="logout.php" class="btn btn-light btn-sm text-primary ms-3 fw-bold">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-light btn-sm text-primary ms-3 fw-bold">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Header Banner -->
    <div class="page-banner banner-matches text-white py-5 px-5">
        <div class="container py-2">
            <h1 class="display-5 fw-bold mb-1">Matches and fixtures</h1>
            <p class="mb-0 text-white-50">Upcoming matches and recent results.</p>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container my-5">

        <!-- Toggle Buttons -->
        <div class="mb-4">
            <a href="matches.php?tab=upcoming" class="btn <?php echo ($tab == 'upcoming') ? 'btn-primary' : 'btn-outline-primary'; ?> rounded-pill px-4 fw-bold me-2">Upcoming</a>
            <a href="matches.php?tab=results" class="btn <?php echo ($tab == 'results') ? 'btn-primary' : 'btn-outline-primary'; ?> rounded-pill px-4 fw-bold">Results</a>
        </div>

        <?php if ($tab == 'upcoming'): ?>
            <!-- UPCOMING MATCHES TABLE -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7 text-muted">
                            <tr>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Sport</th>
                                <th class="py-3 px-4">Match</th>
                                <th class="py-3 px-4">Venue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $upQuery = "SELECT * FROM matches WHERE status = 'Upcoming' ORDER BY match_date ASC";
                            $upResult = $conn->query($upQuery);

                            if ($upResult && $upResult->num_rows > 0) {
                                while($row = $upResult->fetch_assoc()) {
                                    $matchDisplay = !empty($row['opponent']) ? 'NSBM vs ' . htmlspecialchars($row['opponent']) : 'NSBM vs Opponent';
                                    $dateDisplay = !empty($row['match_date']) ? htmlspecialchars($row['match_date']) : 'Date to be set';
                                    $venueDisplay = !empty($row['venue']) ? htmlspecialchars($row['venue']) : 'NSBM Playground';

                                    echo '<tr>
                                            <td class="px-4 text-muted">' . $dateDisplay . '</td>
                                            <td class="px-4 fw-semibold">' . htmlspecialchars($row['sport']) . '</td>
                                            <td class="px-4 text-dark">' . $matchDisplay . '</td>
                                            <td class="px-4 text-muted">' . $venueDisplay . '</td>
                                          </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center text-muted py-4">No upcoming matches scheduled at the moment.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <!-- RESULTS TABLE -->
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7 text-muted">
                            <tr>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Sport</th>
                                <th class="py-3 px-4">Match</th>
                                <th class="py-3 px-4">Venue</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resQuery = "SELECT * FROM matches WHERE status = 'Completed' ORDER BY match_date DESC";
                            $resResult = $conn->query($resQuery);

                            if ($resResult && $resResult->num_rows > 0) {
                                while($row = $resResult->fetch_assoc()) {
                                    $matchDisplay = !empty($row['opponent']) ? 'NSBM vs ' . htmlspecialchars($row['opponent']) : 'Match Result';
                                    $dateDisplay = !empty($row['match_date']) ? htmlspecialchars($row['match_date']) : 'Date set';
                                    $venueDisplay = !empty($row['venue']) ? htmlspecialchars($row['venue']) : 'NSBM Playground';
                                    $statusDisplay = !empty($row['outcome']) ? htmlspecialchars($row['outcome']) : '-';
                                    $scoreDisplay = !empty($row['score']) ? htmlspecialchars($row['score']) : '-';

                                    echo '<tr>
                                            <td class="px-4 text-muted">' . $dateDisplay . '</td>
                                            <td class="px-4 fw-semibold">' . htmlspecialchars($row['sport']) . '</td>
                                            <td class="px-4 text-dark">' . $matchDisplay . '</td>
                                            <td class="px-4 text-muted">' . $venueDisplay . '</td>
                                            <td class="px-4"><span class="fw-bold text-success">' . $statusDisplay . '</span></td>
                                            <td class="px-4 text-muted">' . $scoreDisplay . '</td>
                                          </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center text-muted py-4">No match results available yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>


    <!-- Site footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-md-5">
                    <img src="images/nsbm-logo.png" class="brand-logo mb-2" alt="NSBM Green University Town">
                    <p class="mb-0 small">Play, train and compete at NSBM Green University, Pitipana.</p>
                </div>
                <div class="col-md-4 small">
                    <a href="clubs.php" class="me-3">Clubs</a>
                    <a href="matches.php" class="me-3">Matches</a>
                    <a href="tournaments.php" class="me-3">Tournaments</a>
                    <a href="contact.php">Contact</a>
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