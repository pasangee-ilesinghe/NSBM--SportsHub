<?php 
session_start(); 
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sports Clubs - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="bg-light">
    <!-- Navbar -->
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

    <!-- Header Banner -->
    <div class="page-banner banner-clubs text-white py-4 px-5">
        <h2>Sports clubs</h2>
        <p>Clubs for every level, from beginners to university representatives.</p>
    </div>

    <div class="container my-4">
        <!-- Live Search Bar -->
        <div class="mb-4">
            <label class="form-label text-muted">Search clubs</label>
            <input type="text" id="searchInput" class="form-control w-50" placeholder="Type a sport, e.g. cricket, chess...">
        </div>

        <div class="row" id="clubRow">
            <?php
            $query = "SELECT * FROM clubs";
            $result = $conn->query($query);
            
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $clubName = htmlspecialchars($row['club_name']);
                    
                    // Truncate description so cards stay short and uniform
                    $clubDesc = $row['description'] ?? '';
                    if (strlen($clubDesc) > 90) {
                        $clubDesc = substr($clubDesc, 0, 90) . '...';
                    }
                    $clubDesc = htmlspecialchars($clubDesc);

                    // Use img1 from the database as the card cover, with a fallback image if empty
                    $imgUrl = !empty($row['img1']) ? $row['img1'] : 'images/OIP1.png';
                    
                    echo '<div class="col-md-3 mb-4 club-card-container" data-name="' . strtolower($clubName) . '">
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
                echo '<p class="text-muted">No clubs found in database.</p>';
            }
            ?>
        </div>
        
        <!-- No results message for live search -->
        <p id="noResults" class="text-muted d-none">No clubs found matching your search.</p>
    </div>

    <!-- JavaScript for Live Instant Filtering -->
    <script>
        const searchInput = document.getElementById('searchInput');
        const clubCards = document.querySelectorAll('.club-card-container');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            clubCards.forEach(card => {
                const clubName = card.getAttribute('data-name');
                if (clubName.includes(query)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        });
    </script>

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