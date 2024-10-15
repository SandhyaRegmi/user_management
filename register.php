<?php

require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    
    if (empty($username) || empty($password) || empty($role)) {
        $errors[] = "All fields are required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    } else {
       
        $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
        if (mysqli_num_rows($result) > 0) {
            $errors[] = "Username already exists.";
        } else {
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashedPassword', '$role')";

            if (mysqli_query($conn, $sql)) {
                header('Location: login.php');
                exit;
            } else {
                $errors[] = "Error creating account: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Register</h2>
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
                <div class="mb-3">
                    <label for="role" class="form-label">User Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="regular">Regular User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">Already registered? Login here.</a>
            </div>
        </div>
    </div>
</body>
</html>
