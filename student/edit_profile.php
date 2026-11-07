<?php
session_start();
if (!isset($_SESSION['user']) || !isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}
include '../db.php';
require_once '../csrf.php';
csrf_protect();
$message = "";
$error = "";

$student_id = $_SESSION['student_id'];

// Fetch current details using the users table and student_id column
$stmt = $conn->prepare("SELECT * FROM users WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;

// Handle Update Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!preg_match("/^[\\p{L}][\\p{L} .'\\-]{1,99}$/u", $name)) {
        $error = "Please enter a valid name (letters only, 2-100 characters).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
    $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE student_id = ?");
    $updateStmt->bind_param("sss", $name, $email, $student_id);
    
    if ($updateStmt->execute()) {
        $message = "Profile updated successfully!";
        $_SESSION['user'] = $name; // Update session name for the navbar
        $student['name'] = $name;
        $student['email'] = $email;
    } else {
        $error = "Error updating profile. Email might already be in use.";
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="student_dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Edit Profile</span>
    </nav>

    <div class="container my-5" style="max-width: 600px;">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h4 class="fw-bold text-primary mb-3">Update Personal Details</h4>

            <?php if($message): ?>
                <div class="alert alert-success py-2 text-center"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger py-2 text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($student): ?>
            <form method="POST" data-validate="student">
<?php echo csrf_field(); ?>
                <input type="hidden" name="update_profile" value="1">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Student ID (Read-only)</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['student_id'] ?? ''); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary fw-bold py-2">Save Changes</button>
                    <a href="change_password.php" class="btn btn-outline-secondary btn-sm">Change Password Instead</a>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-danger">User record not found in the database.</div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../validation.js"></script>
</body>
</html>