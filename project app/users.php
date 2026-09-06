<?php
// Lists user accounts that can sign in to the system.
$page_title = 'User Accounts';
require 'includes/header.php';
// Ensure only administrators can view account records.
require_admin();
// Load user accounts for the administration table.
$users = $pdo->query('SELECT id,full_name,username,email,role,created_at FROM users ORDER BY created_at DESC')->fetchAll();?>
<section class="panel">
    <h2>Registered users</h2>
    <p>New receptionist accounts can be created from the registration page.</p>
    <!-- Registered user account list -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u):?>
                <tr>
                    <td>
                        <?=e($u['full_name'])?>
                    </td>
                    <td>
                        <?=e($u['username'])?>
                    </td>
                    <td>
                        <?=e($u['email'])?>
                    </td>
                    <td>
                        <?=e($u['role'])?>
                    </td>
                    <td>
                        <?=e($u['created_at'])?>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</section>
<?php require 'includes/footer.php'; ?>
