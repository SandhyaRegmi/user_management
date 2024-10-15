<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'regular') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="text-center mb-4">Welcome, <?php echo $_SESSION['username']; ?></h2>
    <div class="d-flex justify-content-between">
        <p>You're logged in as a regular user.</p>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
</body>
</html>
