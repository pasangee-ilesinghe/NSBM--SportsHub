<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
require_once 'csrf.php';
csrf_protect();
$student_name = $_SESSION['user'];
$student_id = $_SESSION['student_id']; // Captured for relational database tracking
$message = "";
$alertType = "";

// Get the tournament ID securely from the URL
$selected_tournament_id = intval($_GET['id'] ?? 0);
$tournament_name = "";
$tournament_sport = "";

if ($selected_tournament_id > 0) {
    $tQuery = $conn->prepare("SELECT tournament_name, sport FROM tournaments WHERE id = ?");
    $tQuery->bind_param("i", $selected_tournament_id);
    $tQuery->execute();
    $tResult = $tQuery->get_result();
    if ($tRow = $tResult->fetch_assoc()) {
        $tournament_name = $tRow['tournament_name'];
        $tournament_sport = $tRow['sport'];
    }
    $tQuery->close();
}


$validTournament = !empty($tournament_name);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$validTournament) {
    $message = "This tournament does not exist. Please choose one from the tournaments page.";
    $alertType = "alert-danger";
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $post_tournament = $tournament_name;
    $category = trim($_POST['category'] ?? '');
    $registration_date = date('Y-m-d');
    $status = 'Registered';

    
    if (!in_array($category, ['Under-19', 'Open Category'], true)) {
        $message = "Please select a valid category.";
        $alertType = "alert-danger";
    } else {

    // Updated INSERT query to include student_id alongside student_name
    $stmt = $conn->prepare("INSERT INTO tournament_registrations (student_id, student_name, tournament_id, tournament_name, sport, category, registration_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssisssss", $student_id, $student_name, $selected_tournament_id, $post_tournament, $tournament_sport, $category, $registration_date, $status);
        if ($stmt->execute()) {
            $message = "Tournament registration successful! Check your dashboard.";
            $alertType = "alert-success";
        } else {
            // Check if it's a duplicate entry error
            if ($stmt->errno == 1062 || $conn->errno == 1062) {
                $message = "You have already registered for this tournament!";
                $alertType = "alert-warning";
            } else {
                $message = "Sorry, something went wrong. Please try again.";
                $alertType = "alert-danger";
            }
        }
        $stmt->close();
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tournament Registration - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="index.php"><img src="images/nsbm-logo.png" class="brand-logo" alt="NSBM"> SportsHub</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-lg-end align-items-lg-center" id="navMenu">
            <a href="index.php" class="text-white text-decoration-none mx-2">Home</a>
            <a href="clubs.php" class="text-white text-decoration-none mx-2">Clubs</a>
            <a href="matches.php" class="text-white text-decoration-none mx-2">Matches</a>
            <a href="tournaments.php" class="text-white text-decoration-none mx-2 fw-bold">Tournaments</a>
            <a href="contact.php" class="text-white text-decoration-none mx-2">Contact</a>
            <a href="student/student_dashboard.php" class="text-white text-decoration-none mx-2 fw-bold">Dashboard</a>
            <a href="logout.php" class="btn btn-light btn-sm text-primary ms-3 fw-bold">Logout</a>
        </div>
    </nav>

    <div class="container my-5" style="max-width: 600px;">
        <a href="tournaments.php" class="text-decoration-none small fw-bold d-inline-block mb-3">&larr; Back to Tournaments</a>
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h3 class="fw-bold text-primary mb-1 text-center">Tournament Registration</h3>
            <p class="text-muted text-center small mb-4">Registering for: <strong><?php echo $validTournament ? htmlspecialchars($tournament_name) : 'No tournament selected'; ?></strong></p>

            <?php if($message): ?>
                <div class="alert <?php echo $alertType; ?> py-2 text-center"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Student Name</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($student_name); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Selected Tournament</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($tournament_name); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Select Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">-- Choose Category --</option>
                        <option value="Under-19">Under-19</option>
                        <option value="Open Category">Open Category</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success fw-bold w-100 py-2 shadow-sm" <?php echo $validTournament ? '' : 'disabled'; ?>>Confirm Registration</button>
            </form>
        </div>
    </div>
    <script src="validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>