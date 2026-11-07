<?php
session_start();
include 'db.php';
require_once 'csrf.php';
csrf_protect();
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $identifier = trim($_POST['username']); 
    $password = $_POST['password'];

    if ($role == 'admin') {
        // Admin Login Check 
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
        
            if (password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_user'] = $identifier;
                
                // ADDED FOR ROLE SECURITY 
                $_SESSION['role'] = 'admin'; 
                
            

                header("Location: admin/dashboard.php");
                exit();
            } else {
                $error = "Invalid administrator credentials!";
            }
        } else {
            $error = "Invalid administrator credentials!";
        }
    } else {
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        
        if (!$stmt) {
            die("SQL Prepare Error: " . $conn->error);
        }

        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user['name']; 
                $_SESSION['student_id'] = $user['student_id']; 
                $_SESSION['student_logged'] = true;
                
                
                $_SESSION['role'] = 'student'; 
                

                header("Location: student/student_dashboard.php");
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "No account found with this email address!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - NSBM SportsHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="auth-page">
    
    <div class="card auth-card border-0 p-4">
        <div class="text-center mb-4">
            <img src="images/nsbm-logo.png" class="auth-logo" alt="NSBM Green University Town">
            <h3 class="fw-bold text-primary">NSBM SportsHub</h3>
            <p class="text-muted small">Please select your portal and sign in</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" data-validate="basic">
<?php echo csrf_field(); ?>
            
            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Login As</label>
                <select name="role" class="form-select fw-bold text-primary" required>
                    <option value="student">Student / User</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            
            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Email / Username</label>
                <input type="text" name="username" class="form-control" placeholder="you@example.com or admin username" required>
            </div>

            
            <div class="mb-4">
                <label class="form-label fw-semibold small text-muted">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">Sign In</button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <p class="small text-muted mb-1">Don't have a student account? <a href="register.php" class="text-decoration-none fw-bold">Register here</a></p>
            <a href="index.php" class="text-decoration-none small text-secondary">← Back to Home Page</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="validation.js"></script>
</body>
</html>