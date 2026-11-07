<?php
include 'db.php';
require_once 'csrf.php';
csrf_protect();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);
    $plain = $_POST['password'] ?? '';
    $password = password_hash($plain, PASSWORD_DEFAULT);

    if (!preg_match("/^[\\p{L}][\\p{L} .'\\-]{1,99}$/u", $name)) {
        $error = "Please enter a valid full name (letters only, 2-100 characters).";
    } elseif (!preg_match('/^\\d{4,10}$/', $student_id)) {
        $error = "Student ID must be 4-10 digits.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($plain) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
    // Prepare statement with error debugging
    $stmt = $conn->prepare("INSERT INTO users (name, student_id, email, password, role) VALUES (?, ?, ?, ?, 'student')");
    
    if ($stmt === false) {
        die("Database Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("ssss", $name, $student_id, $email, $password);
    
    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Error: Email or Student ID already exists or database issue.";
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="auth-page auth-with-nav">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
        <a class="navbar-brand fw-bold" href="index.php"><img src="images/nsbm-logo.png" class="brand-logo" alt="NSBM"> SportsHub</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse justify-content-lg-end align-items-lg-center" id="navMenu">
            <a href="index.php" class="text-white text-decoration-none mx-2">Home</a>
            <a href="clubs.php" class="text-white text-decoration-none mx-2">Clubs</a>
            <a href="matches.php" class="text-white text-decoration-none mx-2">Matches</a>
            <a href="tournaments.php" class="text-white text-decoration-none mx-2">Tournaments</a>
            <a href="contact.php" class="text-white text-decoration-none mx-2">Contact</a>
            <a href="login.php" class="btn btn-light btn-sm text-primary ms-2">Login</a>
        </div>
    </nav>

    <div class="container d-flex justify-content-center align-items-center my-5">
        <div class="card auth-card border-0 p-4">
            <img src="images/nsbm-logo.png" class="auth-logo" alt="NSBM Green University Town">
            <h3 class="fw-bold mb-3 text-center">Create an account</h3>
            
            <?php if(!empty($error)): ?>
                <div class='alert alert-danger'><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" data-validate="student">
<?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Full name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" minlength="6" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none small">Already have an account? Login here</a>
            </div>
        </div>
    </div>
    <script src="validation.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 