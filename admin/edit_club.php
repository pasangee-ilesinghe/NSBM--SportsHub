<?php
session_start();

// Strict Admin Role Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db.php';
require_once '../csrf.php';
csrf_protect();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_clubs.php");
    exit();
}

// Fetch existing club data
$stmt = $conn->prepare("SELECT * FROM clubs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$club = $result->fetch_assoc();

if (!$club) {
    header("Location: manage_clubs.php");
    exit();
}

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $venue = $_POST['venue'];
    $master_in_charge = $_POST['master_in_charge'];

    $update_stmt = $conn->prepare("UPDATE clubs SET name=?, category=?, description=?, venue=?, master_in_charge=? WHERE id=?");
    $update_stmt->bind_param("sssssi", $name, $category, $description, $venue, $master_in_charge, $id);
    
    if ($update_stmt->execute()) {
        header("Location: manage_clubs.php?success=updated");
        exit();
    } else {
        $error = "Error updating club.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Club - Admin Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Edit Club</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form method="POST" class="card p-4 shadow-sm" data-validate="basic">
<?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Club Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($club['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($club['category']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($club['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Venue</label>
                <input type="text" name="venue" class="form-control" value="<?php echo htmlspecialchars($club['venue']); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Master-in-Charge</label>
                <input type="text" name="master_in_charge" class="form-control" value="<?php echo htmlspecialchars($club['master_in_charge']); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Club</button>
            <a href="manage_clubs.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <script src="../validation.js"></script>
</body>
</html>