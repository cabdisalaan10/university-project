<?php
// Allows the signed-in user to update their personal account details.
$page_title = 'My Profile';
require 'includes/header.php';

// Save account details and optionally change the password.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    try {
        $statement = $pdo->prepare(
            'UPDATE users SET full_name = ?, username = ?, email = ? WHERE id = ?'
        );
        $statement->execute([$full_name, $username, $email, $_SESSION['user']['id']]);

        // Apply a new password only when the user entered one.
        if ($_POST['new_password'] !== '') {
            $statement = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $statement->execute([
            password_hash($_POST['new_password'], PASSWORD_DEFAULT),
            $_SESSION['user']['id'],
            ]);
        }
        $statement = $pdo->prepare(
            'SELECT id, full_name, username, email, role FROM users WHERE id = ?'
        );
        $statement->execute([$_SESSION['user']['id']]);
        // Keep the session data in sync with the saved account details.
        $_SESSION['user'] = $statement->fetch(PDO::FETCH_ASSOC);

        flash('Your profile has been updated.');
        redirect('profile.php');
    } catch (PDOException $exception) {
        $error = 'Username or email is already used by another account.';
    }
}
$user = $_SESSION['user'];
?>
<!-- User profile summary and edit form -->
<div class="profile-layout">
    <section class="profile-summary panel">
        <div class="profile-avatar">
            <?= e(strtoupper(substr($user['full_name'], 0, 1))) ?>
        </div>
        <h2>
            <?= e($user['full_name']) ?>
        </h2>
        <p>
            <?= e($user['role']) ?>
        </p>
        <span>
            <?= e($user['email']) ?>
        </span>
    </section>
    <section class="panel">
        <h2>Personal information</h2>
        <?php if (isset($error)): ?>
        <div class="error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>
    <form method="post" class="form-grid">
        <label>
            Full name
            <input required name="full_name" value="<?= e($user['full_name']) ?>">
        </label>
        <label>
            Username
            <input required name="username" value="<?= e($user['username']) ?>">
        </label>
        <label>
            Email
            <input required type="email" name="email" value="<?= e($user['email']) ?>">
        </label>
        <label>
            New password <small>(leave blank to keep current)</small>
            <input minlength="6" type="password" name="new_password">
        </label>
        <button>Save profile</button>
    </form>
</section>
</div>
<?php require 'includes/footer.php'; ?>
