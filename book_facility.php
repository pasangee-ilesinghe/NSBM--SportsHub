<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php'; 
require_once 'csrf.php';
csrf_protect();
$message = "";
$alertType = "";

// Handle form submission and save to database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_name = $_SESSION['user']; 
    $student_id = $_SESSION['student_id']; // Captured for relational database tracking
    $facility_name = trim($_POST['facility_name']);
    $booking_date = $_POST['booking_date'];
    $status = 'Pending'; 

    // server-side validation (the HTML form alone can be bypassed)
    $allowedFacilities = ['Indoor Stadium', 'Swimming Pool', 'Gymnasiums', 'Playgrounds and pavilions'];
    $dateOk = DateTime::createFromFormat('Y-m-d', $booking_date) !== false;

    if (!in_array($facility_name, $allowedFacilities, true) || !$dateOk) {
        $message = "Please choose a valid facility and date.";
        $alertType = "alert-danger";
    } elseif ($booking_date < date('Y-m-d')) {
        $message = "You cannot book a facility for a date that has already passed.";
        $alertType = "alert-danger";
    } else {
        // stop double-booking - one approved booking per facility per day
        $chk = $conn->prepare("SELECT id FROM facility_bookings WHERE facility_name = ? AND booking_date = ? AND status = 'Approved'");
        $chk->bind_param("ss", $facility_name, $booking_date);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $message = "Sorry, that facility is already booked on this date. Please pick another date.";
            $alertType = "alert-warning";
        } else {
            // Updated to insert student_id along with student_name
            $stmt = $conn->prepare("INSERT INTO facility_bookings (student_id, student_name, facility_name, booking_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $student_id, $student_name, $facility_name, $booking_date, $status);
            if ($stmt->execute()) {
                $message = "Booking request submitted successfully! Waiting for admin approval.";
                $alertType = "alert-success";
            } else {
                $message = "Error submitting booking request. Please try again.";
                $alertType = "alert-danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Facility - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="bg-light">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4 shadow-sm">
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

    <!-- Booking Form Content -->
    <div class="container my-5">
        <div class="booking-stage">
            <!-- Facility cards: tap one to choose it in the form -->
            <button type="button" class="facility-card fc-pool" data-facility="Swimming Pool" aria-pressed="false">
                <img src="images/doodles/facility-pool.svg" alt="Swimming Pool"><span class="fc-label">Swimming Pool</span>
            </button>
            <button type="button" class="facility-card fc-gym" data-facility="Gymnasiums" aria-pressed="false">
                <img src="images/doodles/facility-gym.svg" alt="Gymnasium"><span class="fc-label">Gymnasium</span>
            </button>
            <button type="button" class="facility-card fc-stadium" data-facility="Indoor Stadium" aria-pressed="false">
                <img src="images/doodles/facility-stadium.svg" alt="Indoor Stadium"><span class="fc-label">Indoor Stadium</span>
            </button>
            <button type="button" class="facility-card fc-play" data-facility="Playgrounds and pavilions" aria-pressed="false">
                <img src="images/doodles/facility-playground.svg" alt="Playground and pavilion"><span class="fc-label">Playground &amp; Pavilion</span>
            </button>

        <div class="card booking-card border-0 rounded-4 p-4 bg-white">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">Facility & Equipment Booking</h3>
                <p class="text-muted small">Select your campus facility and required date. This will be sent to the admin panel for review.</p>
                <p class="fc-tip">Tip: tap a facility card to choose it.</p>
            </div>

            <?php if($message): ?>
                <div class="alert <?php echo $alertType; ?> py-2 small text-center"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Select Facility</label>
                    <select name="facility_name" class="form-select form-select-lg" required>
                        <option value="">-- Choose Facility --</option>
                        <option value="Indoor Stadium">Indoor Stadium </option>
                        <option value="Swimming Pool">Swimming Pool </option>
                        <option value="Gymnasiums">Gymnasiums </option>
                        <option value="Playgrounds and pavilions">Playgrounds and pavilions </option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small text-muted">Required Date</label>
                    <input type="date" name="booking_date" class="form-control form-control-lg" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-3 shadow-sm">Submit Booking Request</button>
            </form>
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
    <script>
    // tapping a facility card selects it in the dropdown (and the dropdown highlights its card)
    (function () {
        var select = document.querySelector('select[name="facility_name"]');
        var cards = document.querySelectorAll('.facility-card');
        function sync() {
            cards.forEach(function (card) {
                var on = card.getAttribute('data-facility') === select.value;
                card.classList.toggle('selected', on);
                card.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
        }
        cards.forEach(function (card) {
            card.addEventListener('click', function () {
                select.value = card.getAttribute('data-facility');
                select.dispatchEvent(new Event('change', { bubbles: true }));
                sync();
                if (window.innerWidth < 992) { select.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            });
        });
        select.addEventListener('change', sync);
        sync();
    })();
    </script>
    <script src="validation.js"></script>
</body>
</html>