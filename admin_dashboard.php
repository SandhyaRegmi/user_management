<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;


    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } else {
        if ($userId) {
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = '$username', password = '$hashedPassword' WHERE id = '$userId'";
            if (mysqli_query($conn, $sql)) {
                $success = "User updated successfully!";
            } else {
                $errors[] = "Error updating user: " . mysqli_error($conn);
            }
        } else {
            
            $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
            if (mysqli_num_rows($result) > 0) {
                $errors[] = "Username already exists.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashedPassword', 'regular')";
                if (mysqli_query($conn, $sql)) {
                    $success = "User added successfully!";
                } else {
                    $errors[] = "Error adding user: " . mysqli_error($conn);
                }
            }
        }
    }
}


$result = mysqli_query($conn, "SELECT * FROM users WHERE role = 'regular'");


if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id = '$userId'");
    header('Location: admin_dashboard.php');
    exit;
}


$editUser = null;
if (isset($_GET['edit'])) {
    $userId = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$userId'");
    $editUser = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        
        function confirmDelete(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                window.location.href = 'admin_dashboard.php?delete=' + userId;
            }
        }
    </script>
</head>

<body class="container mt-5">
    <h2 class="text-center mb-4">Admin Dashboard</h2>

    
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title"><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h4>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?php echo $editUser['username'] ?? ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <?php if ($editUser): ?>
                    <input type="hidden" name="user_id" value="<?php echo $editUser['id']; ?>">
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><?php echo $editUser ? 'Update User' : 'Add User'; ?></button>
                    <?php if ($editUser): ?>
                        <a href="admin_dashboard.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    
    <h4 class="mb-3">Manage Regular Users</h4>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <button onclick="confirmDelete(<?php echo $user['id']; ?>)" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="logout.php" class="btn btn-secondary mt-3">Logout</a>
</body>

</html>