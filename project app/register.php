<?php
// Allows a new receptionist account to be registered.
require_once 'includes/config.php';
if (logged_in()) {
    redirect('dashboard.php');
}
// Validate and create a receptionist account.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    if (strlen($_POST['password']) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $pdo->prepare("INSERT INTO users(full_name,username,email,password,role) VALUES(?,?,?,?, 'receptionist')")->execute([$name, $username, $email, password_hash($_POST['password'], PASSWORD_DEFAULT)]);
            flash('Account created. Please sign in.');
            redirect('login.php');
        } catch (PDOException $e) {
            $error = 'Username or email already exists.';
        }
    }
}
?>
<!-- Registration interface -->
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Register | HotelMS</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="auth">
        <form class="auth-card" method="post">
            <div class="brand">Hotel<span>MS</span>
            </div>
            <h1>Create account</h1>
            <?php if (isset($error)):?>
                <div class="error">
                    <?=e($error)?>
                </div>
            <?php endif;?>

<label>
            Full name
            <input required name="full_name">
            </label>

<label>
            Username
            <input required name="username">
            </label>

<label>
            Email
            <input required type="email" name="email">
            </label>

<label>
            Password
            <input required minlength="6" type="password" name="password">
            </label>
            <button>Create account</button>
            <div class="auth-links">
                <a href="login.php">Back to login</a>
            </div>
        </form>
    </body>
</html>
