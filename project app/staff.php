<?php
// Allows administrators to manage hotel staff records.
$page_title = 'Staff Management';
require 'includes/header.php';
require_admin();
// Process staff creation, editing, and deletion requests.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $pdo->prepare('DELETE FROM staff WHERE id=?')->execute([$_POST['id']]);
        flash('Staff member deleted.');
    } else {
        $id = (int)($_POST['id'] ?? 0);
        $d = [trim($_POST['full_name']), trim($_POST['phone']), trim($_POST['email']), trim($_POST['job_role']), trim($_POST['responsibility']), $_POST['status']];
        if ($id) {
            $d[] = $id;
            $pdo->prepare('UPDATE staff SET full_name=?,phone=?,email=?,job_role=?,responsibility=?,status=? WHERE id=?')->execute($d);
            flash('Staff updated.');
        } else {
            $pdo->prepare('INSERT INTO staff(full_name,phone,email,job_role,responsibility,status) VALUES(?,?,?,?,?,?)')->execute($d);
            flash('Staff added.');
        }
    }
    redirect('staff.php');
}
// Load all staff and optionally load one record into edit mode.
$staff = $pdo->query('SELECT * FROM staff ORDER BY full_name')->fetchAll();
$edit = null;
if (isset($_GET['edit'])) {
    // Load the selected staff member and populate the form in edit mode.
    $s = $pdo->prepare('SELECT * FROM staff WHERE id=?');
    $s->execute([$_GET['edit']]);
    $edit = $s->fetch();
}?>
<!-- Staff form and staff records table -->
<div class="grid-2">
    <section class="panel">
        <h2>
            <?=$edit ? 'Edit staff' : 'Add staff member'?>
        </h2>
        <form method="post" class="form-grid">
            <input type="hidden" name="id" value="<?=$edit['id'] ?? ''?>">

<label>
            Name
            <input required name="full_name" value="<?=e($edit['full_name'] ?? '')?>">
            </label>

<label>
            Phone
            <input required name="phone" value="<?=e($edit['phone'] ?? '')?>">
            </label>

<label>
            Email
            <input type="email" name="email" value="<?=e($edit['email'] ?? '')?>">
            </label>

<label>
            Role
            <input required name="job_role" placeholder="e.g. Receptionist" value="<?=e($edit['job_role'] ?? '')?>">
            </label>

<label class="full">
            Responsibility
            <input name="responsibility" value="<?=e($edit['responsibility'] ?? '')?>">
            </label>

<label>
            Status
            <select name="status">
                    <option <?=($edit['status'] ?? '') === 'Active' ? 'selected' : ''?>>Active</option>
                    <option <?=($edit['status'] ?? '') === 'Inactive' ? 'selected' : ''?>>Inactive</option>
                </select>
            </label>
            <button>Save staff</button>
        </form>
    </section>
    <section class="panel">
        <h2>Staff records</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staff as $x):?>
                    <tr>
                        <td>
                            <?=e($x['full_name'])?>
                        </td>
                        <td>
                            <?=e($x['job_role'])?>
                        </td>
                        <td>
                            <?=e($x['phone'])?>
                        </td>
                        <td>
                            <?=e($x['status'])?>
                        </td>
                        <td>
                            <a href="staff.php?edit=<?=$x['id']?>">Edit</a>
                            <form class="inline" method="post">
                                <input type="hidden" name="id" value="<?=$x['id']?>">
                                <button class="link danger" name="delete" data-confirm="Delete this staff member?">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </section>
</div>
<?php require 'includes/footer.php'; ?>
