<?php
// Creates a password-reset request for a registered user.
require_once 'includes/config.php';
// Create a temporary reset token when the email exists.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $user = $pdo->prepare('SELECT id FROM users WHERE email=?');
    $user->execute([$email]);
    if ($user->fetch()) {
        // Store a temporary token for the password-reset process.
        $token = bin2hex(random_bytes(24));
        $pdo->prepare('INSERT INTO password_resets(email,token,expires_at) VALUES(?,?,DATE_ADD(NOW(), INTERVAL 1 HOUR))')->execute([$email, $token]);
        $notice = 'Reset request saved. In a real deployed system, a reset link is sent by email.';
    } else {
        // Use a neutral message so registered accounts cannot be identified.
        $notice = 'If that email exists, a reset request has been created.';
    }
}
?>
<!-- Password reset request interface -->
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Reset password | HotelMS</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="auth">
        <form class="auth-card" method="post">
            <div class="brand">Hotel<span>MS</span>
            </div>
            <h1>Reset password</h1>
            <p>Enter your registered email.</p>
            <?php if (isset($notice)):?>
                <div class="alert">
                    <?=e($notice)?>
                </div>
            <?php endif;?>

<label>
            Email
            <input required type="email" name="email">
            </label>
            <button>Request reset</button>
            <div class="auth-links">
                <a href="login.php">Back to login</a>
            </div>
        </form>
    </body>
</html>
