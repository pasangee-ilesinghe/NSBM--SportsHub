<?php
session_start();
include 'db.php'; 
require_once 'csrf.php';
csrf_protect();
$message = "";
$alertType = "";

$nameQuery = $_GET['name'] ?? 'Badminton Club';
$decodedName = urldecode($nameQuery);

// if the URL passes just the base name, automatically append " Club" if needed
$trimmedName = trim($decodedName);
if (in_array(strtolower($trimmedName), ['cricket', 'badminton', 'rugby', 'athletics', 'swimming', 'football', 'basketball', 'volleyball'])) {
    $decodedName = $trimmedName . ' Club';
}

// Fetch the specific club from MySQL dynamically (including recent_results and banner_img columns)
$stmt = $conn->prepare("SELECT * FROM clubs WHERE club_name = ? LIMIT 1");
$stmt->bind_param("s", $decodedName);
$stmt->execute();
$club = $stmt->get_result()->fetch_assoc();

if (!$club) {
    $stmt = $conn->prepare("SELECT * FROM clubs WHERE club_name LIKE ? ORDER BY id LIMIT 1");
    $searchTerm = "%" . $trimmedName . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $club = $stmt->get_result()->fetch_assoc();
}

// Fallback safety if the club isn't found in the database
if (!$club) {
    $club = [
        'id' => 0,
        'club_name' => $decodedName,
        'category' => 'Clubs / Sports',
        'description' => 'Dynamic and active sports club at NSBM Green University.',
        'master_in_charge' => 'Mr. Thisal Yatawara',
        'mistress_in_charge' => 'Ms. Isuri Caldera',
        'venue' => 'NSBM Green University, Pitipana',
        'email' => 'inquiries@nsbm.ac.lk',
        'recent_results' => 'No recent results posted yet.',
        'banner_img' => 'images/OIP1.png',
        'img1' => 'images/OIP2.webp',
        'img2' => 'images/OIP3.jpg',
        'img3' => 'images/OIP4.jpg'
    ];
}

// Handle the "Join Club" Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['join_club'])) {
    if (!isset($_SESSION['user']) || empty($_SESSION['user']) || !isset($_SESSION['student_id'])) {
        header("Location: login.php");
        exit();
    }
    
    $student_name = $_SESSION['user'];
    $student_id = $_SESSION['student_id']; // Captured for relational database tracking
    $club_id = (int)$club['id'];
    if ($club_id === 0) { 
        header("Location: clubs.php");
        exit();
    }

    $check = $conn->prepare("SELECT * FROM club_memberships WHERE student_id = ? AND club_id = ?");
    $check->bind_param("si", $student_id, $club_id);
    $check->execute();
    
    if ($check->get_result()->num_rows == 0) {
        $status = 'Pending';
        // Updated insert query to include student_id alongside student_name
        $insert = $conn->prepare("INSERT INTO club_memberships (student_id, student_name, club_id, joined_date, status) VALUES (?, ?, ?, NOW(), ?)");
        $insert->bind_param("ssis", $student_id, $student_name, $club_id, $status);
        if ($insert->execute()) {
            $message = "Membership request submitted successfully! It is pending admin approval.";
            $alertType = "alert-success";
        } else {
            $message = "Error submitting request. Please try again.";
            $alertType = "alert-danger";
        }
    } else {
        $message = "You have already joined or requested this club!";
        $alertType = "alert-warning";
    }
}

$isSwimmingClub = (stripos($club['club_name'], 'Swimming') !== false);

// Fetch Regular Weekly Schedules and Special Sessions using the resolved club name
$clubSearchName = "%" . trim($club['club_name']) . "%";

$recQuery = $conn->prepare("SELECT * FROM club_schedules WHERE club_name LIKE ?");
$recQuery->bind_param("s", $clubSearchName);
$recQuery->execute();
$recResult = $recQuery->get_result();

$specQuery = $conn->prepare("SELECT * FROM club_special_sessions WHERE club_name LIKE ? ORDER BY session_date ASC");
$specQuery->bind_param("s", $clubSearchName);
$specQuery->execute();
$specResult = $specQuery->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($club['club_name']); ?> - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="bg-light">

    <!-- Top Navigation Bar with Fixed Root Paths -->
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
                <!-- Student Logged In -->
                <a href="student/student_dashboard.php" class="text-white text-decoration-none mx-2 fw-bold">Dashboard</a>
                <a href="logout.php" class="btn btn-light btn-sm text-primary ms-3 fw-bold">Logout</a>

            <?php else: ?>
                <!-- Not Logged In -->
                <a href="login.php" class="btn btn-light btn-sm text-primary ms-3 fw-bold">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Header Banner using External CSS Class & Dedicated Banner Image -->
    <div class="text-white py-5 px-5 club-banner" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo htmlspecialchars(!empty($club['banner_img']) ? $club['banner_img'] : 'https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?auto=format&fit=crop&w=1350&q=80'); ?>');">
        <div class="container py-3">
            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($club['club_name']); ?></h1>
            <p class="mb-0 text-white-50">Clubs / <?php echo str_replace(' Club', '', $club['club_name']); ?></p>
        </div>
    </div>

    <div class="container my-5">
        
        <?php if($message): ?>
            <div class="alert <?php echo $alertType; ?> py-2 text-center mb-4"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row">
            
            <!-- Left Column: Unique Swipeable Image Carousel -->
            <div class="col-md-7 mb-4">
                <div id="clubCarousel" class="carousel slide shadow-sm rounded overflow-hidden" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#clubCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                        <button type="button" data-bs-target="#clubCarousel" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#clubCarousel" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?php echo htmlspecialchars($club['img1'] ?? 'images/OIP1.png'); ?>" class="d-block w-200" alt="Slide 1" style="height: 350px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="<?php echo htmlspecialchars($club['img2'] ?? 'images/OIP2.webp'); ?>" class="d-block w-100" alt="Slide 2" style="height: 350px; object-fit: cover;">
                        </div>
                        <div class="carousel-item">
                            <img src="<?php echo htmlspecialchars($club['img3'] ?? 'images/OIP3.jpg'); ?>" class="d-block w-100" alt="Slide 3" style="height: 350px; object-fit: cover;">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#clubCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#clubCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <!-- Right Column: Dynamic Sidebar & Staff Information -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <!-- Join Club Form Button -->
                        <form method="POST">
<?php echo csrf_field(); ?>
                            <input type="hidden" name="join_club" value="1">
                            <?php if (isset($_SESSION['user'])): ?>
                                <button type="submit" class="btn btn-success fw-bold px-4 mb-3 w-100 shadow-sm">Join club</button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-success fw-bold px-4 mb-3 w-100 shadow-sm">Login to join club</a>
                            <?php endif; ?>
                        </form>

                        <?php if ($isSwimmingClub): ?>
                            <!-- Pool Fee Calculator Widget -->
                            <div class="border rounded p-3 mb-3 bg-light">
                                <h6 class="fw-bold mb-2">Pool fee calculator</h6>
                                <label class="form-label text-muted small">Hours</label>
                                <input type="number" id="poolHours" class="form-control form-control-sm mb-2" value="1" min="1">
                                <p class="text-muted small mb-1">Rs. 100 per hour</p>
                                <h5 class="fw-bold text-primary mb-0">Total: Rs. <span id="poolTotal">100</span></h5>
                            </div>
                        <?php endif; ?>

                        <!-- Master in Charge -->
                        <?php if (!empty($club['master_in_charge'])): ?>
                            <p class="mb-1 text-muted small"><strong>Master in charge:</strong> <?php echo htmlspecialchars($club['master_in_charge']); ?></p>
                        <?php endif; ?>

                        <!-- Mistress in Charge -->
                        <?php if (!empty($club['mistress_in_charge'])): ?>
                            <p class="mb-1 text-muted small"><strong>Mistress in charge:</strong> <?php echo htmlspecialchars($club['mistress_in_charge']); ?></p>
                        <?php endif; ?>

                        <p class="mb-1 text-muted small"><strong>Venue:</strong> <?php echo htmlspecialchars($club['venue'] ?? 'NSBM Green University, Pitipana'); ?></p>
                        <p class="mb-0 text-muted small"><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($club['email'] ?? 'inquiries@nsbm.ac.lk'); ?>"><?php echo htmlspecialchars($club['email'] ?? 'inquiries@nsbm.ac.lk'); ?></a></p>
                    </div>
                </div>

                <!-- Recent Results Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">Recent results</h6>
                        <?php if (!empty($club['recent_results'])): ?>
                            <p class="text-secondary small mb-0"><?php echo nl2br(htmlspecialchars($club['recent_results'])); ?></p>
                        <?php else: ?>
                            <p class="text-muted small mb-0">Recent results will be added soon.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Bottom Section: About and Two Training Schedule Cards -->
        <div class="row mt-4">
            <div class="col-12">
                <h4 class="fw-bold">About the club</h4>
                <p class="text-muted"><?php echo htmlspecialchars($club['description']); ?></p>

                <h4 class="fw-bold mt-5 mb-3">Training Schedules & Events</h4>

                <div class="row">
                    <!-- Card 1: Regular Weekly Schedules -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold mb-3 text-primary">Regular Weekly Schedule</h5>
                            <?php if ($recResult && $recResult->num_rows > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while($rec = $recResult->fetch_assoc()): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-0">
                                            <div>
                                                <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($rec['session_day']); ?></span>
                                                <strong class="text-dark"><?php echo htmlspecialchars($rec['time']); ?></strong>
                                            </div>
                                            <span class="text-muted small">📍 <?php echo htmlspecialchars($rec['venue']); ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted italic mb-0">No recurring weekly schedule posted yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card 2: Special Sessions (With Dates) -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold mb-3 text-success">Special Sessions & Events</h5>
                            <?php if ($specResult && $specResult->num_rows > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while($spec = $specResult->fetch_assoc()): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-0">
                                            <div>
                                                <span class="badge bg-success me-2"><?php echo htmlspecialchars($spec['session_date']); ?></span>
                                                <div>
                                                    <strong class="text-dark"><?php echo htmlspecialchars($spec['description']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($spec['time']); ?></small>
                                                </div>
                                            </div>
                                            <span class="text-muted small">📍 <?php echo htmlspecialchars($spec['venue']); ?></span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted italic mb-0">No upcoming special sessions scheduled.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript for Pool Fee Calculator -->
    <script>
        const poolHours = document.getElementById('poolHours');
        const poolTotal = document.getElementById('poolTotal');

        if (poolHours && poolTotal) {
            poolHours.addEventListener('input', function() {
                let hours = parseInt(poolHours.value) || 0;
                poolTotal.innerText = hours * 100;
            });
        }
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
