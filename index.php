<?php 
session_start(); 
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NSBM SportsHub</title>
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

    <!-- Original Full-Width Hero Section with Background Image -->
    <div class="hero-section hero-home text-white text-center py-5 px-4">
        <h1 class="display-4 fw-bold mb-3">Play, train, compete at NSBM Green University</h1>
        <p class="lead mb-4">Your campus. Your sports. Your victory.</p>
        <div>
            <a href="clubs.php" class="btn btn-success fw-bold px-4 py-2 me-2">Browse clubs</a>
            <?php if (!isset($_SESSION['user']) && !isset($_SESSION['student_logged']) && !isset($_SESSION['admin_logged'])): ?>
                <a href="register.php" class="btn btn-outline-light fw-bold px-4 py-2">Create account</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container my-5">
        
        <!-- About the campus (uses the campus photo) -->
        <div class="row align-items-center g-4 mb-5 about-campus">
            <div class="col-md-6">
                <img src="images/campus.jpg" alt="Aerial view of NSBM Green University campus, Pitipana">
            </div>
            <div class="col-md-6">
                <h3 class="section-title mb-3">Sport on a green campus</h3>
                <p class="text-muted">NSBM Green University sits in Pitipana, Homagama. Our clubs train across the campus, from the indoor stadium and six-lane pool to the gyms, playgrounds and pavilions.</p>
                <span class="chip">Indoor Stadium</span><span class="chip">Swimming Pool</span><span class="chip">Gymnasiums</span><span class="chip">Playgrounds</span>
            </div>
        </div>

        <!-- Featured Clubs Section -->
        <h3 class="section-title mb-4">Featured clubs</h3>

        <div class="row mb-5">
            <?php
            // Fetch first 4 featured clubs from database
            $query = "SELECT * FROM clubs LIMIT 4";
            $result = $conn->query($query);
            
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $clubName = htmlspecialchars($row['club_name']);
                    
                    // Truncate description so featured cards stay short and uniform
                    $clubDesc = $row['description'] ?? '';
                    if (strlen($clubDesc) > 100) {
                        $clubDesc = substr($clubDesc, 0, 100) . '...';
                    }
                    $clubDesc = htmlspecialchars($clubDesc);

                    $imgUrl = !empty($row['img1']) ? $row['img1'] : 'https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?auto=format&fit=crop&w=500&q=80';
                    
                    echo '<div class="col-md-3 mb-4">
                            <div class="card shadow-sm h-100 border-0 rounded-4 overflow-hidden">
                                <div style="position: relative; height: 160px;">
                                    <img src="' . htmlspecialchars($imgUrl) . '" class="w-100 h-100 object-fit-cover" alt="' . $clubName . '">
                                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 10px 15px;">
                                        <h5 class="text-white fw-bold m-0">' . str_replace(' Club', '', $clubName) . '</h5>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="fw-bold text-dark">' . $clubName . '</h6>
                                        <p class="text-muted small mb-3">' . $clubDesc . '</p>
                                    </div>
                                    <div>
                                        <a href="club_detail.php?name=' . urlencode($row['club_name']) . '" class="btn btn-success btn-sm px-3">View club</a>
                                    </div>
                                </div>
                            </div>
                          </div>';
                }
            } else {
                echo '<p class="text-muted">No featured clubs found.</p>';
            }
            ?>
        </div>

        <!-- Facilities on Campus Section -->
        <h3 class="section-title mb-4">Facilities on campus</h3>
        <a href="book_facility.php" class="text-decoration-none text-dark">
            <div class="row mb-5">
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100 p-3">
                        <h5 class="fw-bold">Indoor Stadium</h5>
                        <p class="text-muted small mb-0">Courts and events.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100 p-3">
                        <h5 class="fw-bold">Swimming Pool</h5>
                        <p class="text-muted small mb-0">Six lanes. Rs. 100 per hour.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100 p-3">
                        <h5 class="fw-bold">Gymnasiums</h5>
                        <p class="text-muted small mb-0">Indoor and outdoor gym. Rs. 100 per hour.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100 p-3">
                        <h5 class="fw-bold">Playgrounds and pavilions</h5>
                        <p class="text-muted small mb-0">Outdoor training and matches.</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Next Event Section -->
        <h3 class="section-title mb-4">Next event</h3>
        <div class="card shadow-sm border-0 p-4">
            <h4 class="fw-bold">NSBM U-Sports Athletic Championship</h4>
            <p class="text-muted">An inter-university championship open to state and non-state campuses across Sri Lanka. Hosted by the NSBM Athletic Club.</p>
            <a href="tournaments.php" class="btn btn-primary btn-sm fw-bold w-25 mt-2">View tournaments</a>
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