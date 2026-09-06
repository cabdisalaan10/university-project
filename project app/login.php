<?php
// Authenticates a user and starts their application session.
require_once 'includes/config.php';
$hotel = hotel_settings();
if (logged_in()) {
    redirect('dashboard.php');
}
// Validate the submitted username and password.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username=? LIMIT 1');
    $stmt->execute([trim($_POST['username'])]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($_POST['password'], $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        redirect('dashboard.php');
    }
    $error = 'Invalid username or password.';
}
?>
<!-- Login interface -->
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Login | <?=e($hotel['hotel_name'])?>
        </title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="auth">
        <form class="auth-card" method="post">
            <?php if (!empty($hotel['logo_path'])):?>
                <img class="auth-logo" src="<?=e($hotel['logo_path'])?>" alt="Hotel logo">
            <?php endif;?>
            <div class="brand auth-brand">
                <?=e($hotel['hotel_name'])?>
            </div>
            <h1>Welcome back</h1>
            <p>Sign in to manage your hotel.</p>
            <?php if (isset($error)):?>
                <div class="error">
                    <?=e($error)?>
                </div>
            <?php endif;?>

<label>
            Username
            <input required name="username" autofocus>
            </label>

<label>
            Password
            <input required type="password" name="password">
            </label>
            <button>Sign in</button>
            <div class="auth-links">
                <a href="register.php">Create account</a>
                <a href="forgot_password.php">Forgot password?</a>
            </div>
           
        </form>
    </body>
</html>
