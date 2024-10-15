<?php
session_start();
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    
    if (empty($username) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
           
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: user_dashboard.php');
                }
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Login</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="register.php" class="text-decoration-none">Don't have an account? Register here.</a>
            </div>
        </div>
    </div>
</body>
</html>
