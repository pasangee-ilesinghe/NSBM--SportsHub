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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Fetch current password hash from users table
        $stmt = $conn->prepare("SELECT password FROM users WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res && password_verify($current_password, $res['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE student_id = ?");
            $updateStmt->bind_param("ss", $new_hash, $student_id);
            
            if ($updateStmt->execute()) {
                $message = "Password changed successfully!";
            } else {
                $error = "Database error updating password.";
            }
        } else {
            $error = "Incorrect current password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="student_dashboard.php">← Back to Dashboard</a>
        <span class="text-white">Change Password</span>
    </nav>

    <div class="container my-5" style="max-width: 600px;">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h4 class="fw-bold text-primary mb-3">Security & Password Settings</h4>

            <?php if($message): ?>
                <div class="alert alert-success py-2 text-center"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-danger py-2 text-center"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
                <input type="hidden" name="change_password" value="1">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">New Password</label>
                    <input type="password" name="new_password" class="form-control" minlength="6" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary fw-bold py-2">Update Password</button>
                    <a href="edit_profile.php" class="btn btn-outline-secondary btn-sm">Back to Edit Profile</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../validation.js"></script>
</body>
</html>