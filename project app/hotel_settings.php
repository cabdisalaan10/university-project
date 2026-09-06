<?php
// Allows an administrator to manage the hotel name, contacts, and branding.
$page_title = 'Hotel Settings';
require 'includes/header.php';
require_admin();

$settings = hotel_settings();

// Validate the uploaded logo and save the hotel profile.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logo_path = $settings['logo_path'];

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        ];
        $mime_type = mime_content_type($_FILES['logo']['tmp_name']);

        if (!isset($allowed_types[$mime_type])) {
            $error = 'Logo must be a JPG, PNG, or WEBP image.';
        } elseif ($_FILES['logo']['size'] > 2 * 1024 * 1024) {
            $error = 'Logo file must be 2 MB or smaller.';
        } else {
            $filename = 'assets/uploads/hotel-logo-' . time() . '.' . $allowed_types[$mime_type];
            move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . DIRECTORY_SEPARATOR . $filename);
            $logo_path = $filename;
        }
    }

    // Save the hotel profile after the optional logo passes validation.
    if (!isset($error)) {
        $statement = $pdo->prepare(
            'UPDATE hotel_settings
SET hotel_name = ?, hotel_email = ?, hotel_phone = ?, hotel_address = ?, logo_path = ?
WHERE id = 1'
        );
        $statement->execute([
        trim($_POST['hotel_name']),
        trim($_POST['hotel_email']),
        trim($_POST['hotel_phone']),
        trim($_POST['hotel_address']),
        $logo_path,
        ]);

        flash('Hotel profile and branding updated.');
        redirect('hotel_settings.php');
    }
}
?>
<!-- Hotel settings introduction -->
<section class="settings-banner">
    <div class="settings-icon">⚙</div>
    <div>
        <h2>Hotel profile &amp; branding</h2>
        <p>Set the hotel identity shown across your system.</p>
    </div>
</section>
<!-- Hotel branding and contact-information form -->
<section class="panel settings-panel">
    <?php if (isset($error)): ?>
    <div class="error">
        <?= e($error) ?>
    </div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data" class="form-grid">
    <label>
        Hotel name
        <input required maxlength="120" name="hotel_name" value="<?= e($settings['hotel_name']) ?>">
    </label>
    <label>
        Hotel email
        <input type="email" name="hotel_email" value="<?= e($settings['hotel_email']) ?>">
    </label>
    <label>
        Hotel phone
        <input name="hotel_phone" value="<?= e($settings['hotel_phone']) ?>">
    </label>
    <label>
        Hotel address
        <input name="hotel_address" value="<?= e($settings['hotel_address']) ?>">
    </label>
    <label class="full">
        Hotel logo <small>JPG, PNG or WEBP - maximum 2 MB.</small>
        <input accept="image/png,image/jpeg,image/webp" type="file" name="logo">
    </label>
    <?php if (!empty($settings['logo_path'])): ?>
    <div class="current-logo">
        <span>Current logo</span>
        <img src="<?= e($settings['logo_path']) ?>" alt="Current hotel logo">
    </div>
<?php endif; ?>
<button>Save hotel settings</button>
</form>
</section>
<?php require 'includes/footer.php'; ?>
