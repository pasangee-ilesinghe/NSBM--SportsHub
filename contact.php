<?php 
session_start(); 
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
        <a class="navbar-brand fw-bold" href="index.php"><img src="images/nsbm-logo.png" class="brand-logo" alt="NSBM"> SportsHub</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-lg-end align-items-lg-center" id="navMenu">
            <a href="index.php" class="text-white text-decoration-none mx-2">Home</a>
            <a href="clubs.php" class="text-white text-decoration-none mx-2">Clubs</a>
            <a href="matches.php" class="text-white text-decoration-none mx-2">Matches</a>
            <a href="tournaments.php" class="text-white text-decoration-none mx-2">Tournaments</a>
            <a href="contact.php" class="text-white text-decoration-none mx-2">Contact</a>

            <?php if (isset($_SESSION['user']) || isset($_SESSION['student_logged'])): ?>
                <!-- Student is Logged In -->
                <a href="student/student_dashboard.php" class="text-white text-decoration-none mx-2 fw-bold">Dashboard</a>
                <a href="logout.php" class="btn btn-light btn-sm text-primary ms-3 fw-bold">Logout</a>

            <?php else: ?>
                <!-- No One Logged In -->
                <a href="login.php" class="btn btn-light btn-sm text-primary ms-3 fw-bold">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-banner banner-contact text-white py-4 px-5">
        <h2>Contact us</h2>
        <p>Questions about clubs or facilities? Get in touch.</p>
    </div>

    <div class="container my-5">
        <div class="row g-4">
            <!-- Left Column: Location & General Info (Matches your original design) -->
            <div class="col-md-6">
                <h5 class="fw-bold">Location</h5>
                <p class="text-muted">NSBM Green University<br>Mahenwaththa, Pitipana, Homagama, Sri Lanka</p>
                
                <h5 class="fw-bold">Email</h5>
                <p><a href="mailto:inquiries@nsbm.ac.lk">inquiries@nsbm.ac.lk</a></p>
                
                <h5 class="fw-bold">Phone</h5>
                <p class="text-muted">011 544 5000<br>071 244 5000 (WhatsApp)</p>
                
                <img src="images/campus.jpg" class="img-fluid rounded-4 shadow-sm mt-2" alt="NSBM Green University campus">
            </div>

            <!-- Right Column: Replaced Form with Club Contacts & Physical Meeting Info -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold text-primary mb-3">Club Clarifications & Inquiries</h5>
                    <p class="text-muted small">
                        If you have any questions, want more information about specific sports clubs, or need guidance on memberships, students are warmly encouraged to <b>physically meet the respective Master-in-Charge or Mistress-in-Charge</b> during university hours, or consult the <b>top board members</b> of each club.
                    </p>
                </div>

                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3">Club Masters-in-Charge Directory</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Club Name</th>
                                    <th>Master/Mistress-in-Charge</th>
                                    <th>Faculty Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $clubQuery = $conn->query("SELECT club_name, master_in_charge FROM clubs");
                                
                                if ($clubQuery && $clubQuery->num_rows > 0) {
                                    while($club = $clubQuery->fetch_assoc()) {
                                        $mic = !empty($club['master_in_charge']) ? $club['master_in_charge'] : 'Coordinator Assigned';
                                        $slug = strtolower(str_replace(' ', '.', $club['club_name']));
                                        
                                        echo '<tr>
                                                <td class="fw-bold text-primary">' . htmlspecialchars($club['club_name']) . '</td>
                                                <td>' . htmlspecialchars($mic) . '</td>
                                                <td><a href="mailto:sports.' . $slug . '@nsbm.ac.lk" class="text-decoration-none">sports.' . $slug . '@nsbm.ac.lk</a></td>
                                              </tr>';
                                    }
                                } else {
                                    // Fallback sample rows if your clubs table is empty
                                    echo '<tr>
                                            <td class="fw-bold text-primary">Cricket Club</td>
                                            <td>Mr. Chaminda Perera</td>
                                            <td><a href="mailto:sports.cricket@nsbm.ac.lk" class="text-decoration-none">sports.cricket@nsbm.ac.lk</a></td>
                                          </tr>
                                          <tr>
                                            <td class="fw-bold text-primary">Badminton Club</td>
                                            <td>Ms. Tharushi Silva</td>
                                            <td><a href="mailto:sports.badminton@nsbm.ac.lk" class="text-decoration-none">sports.badminton@nsbm.ac.lk</a></td>
                                          </tr>
                                          <tr>
                                            <td class="fw-bold text-primary">Athletics Club</td>
                                            <td>Mr. Nuwan Gunawardena</td>
                                            <td><a href="mailto:sports.athletics@nsbm.ac.lk" class="text-decoration-none">sports.athletics@nsbm.ac.lk</a></td>
                                          </tr>';
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