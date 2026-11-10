<?php
session_start();
include '../db.php';
require_once '../csrf.php';
csrf_protect();
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Select the admin record by username only
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        // Securely verify the password hash
        if (password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_user'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid administrator credentials!";
        }
    } else {
        $error = "Invalid administrator credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/png" href="../images/favicon.png">
</head>
<body class="auth-page auth-admin">
    <div class="card auth-card border-0 p-4">
        <img src="../images/nsbm-logo.png" class="auth-logo" alt="NSBM Green University Town">
        <h3 class="fw-bold mb-3 text-center">Admin Portal</h3>
        <?php if($error): ?>
            <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label small text-muted">Admin Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Login</button>
        </form>
        <div class="text-center mt-3">
            <a href="../index.php" class="text-decoration-none small text-muted">← Back to Student Portal</a>
        </div>
    </div>
    <script src="../validation.js"></script>
</body>
</html>