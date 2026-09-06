<?php
// Allows administrators to add, edit, and delete room records.
$page_title = 'Room Management';
require 'includes/header.php';
require_admin();
// Process add, update, and delete room requests.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $pdo->prepare('DELETE FROM rooms WHERE id=?')->execute([$_POST['id']]);
        flash('Room deleted.');
    } else {
        $id = (int)($_POST['id'] ?? 0);
        $data = [trim($_POST['room_number']), $_POST['room_type_id'], $_POST['price'], $_POST['status'], trim($_POST['notes'])];
        if ($id) {
            $data[] = $id;
            $pdo->prepare('UPDATE rooms SET room_number=?,room_type_id=?,price=?,status=?,notes=? WHERE id=?')->execute($data);
            flash('Room updated.');
        } else {
            $pdo->prepare('INSERT INTO rooms(room_number,room_type_id,price,status,notes) VALUES(?,?,?,?,?)')->execute($data);
            flash('Room added.');
        }
    }
    redirect('rooms.php');
}
// Load room types and room records for the page.
$types = $pdo->query('SELECT * FROM room_types ORDER BY name')->fetchAll();
$rooms = $pdo->query('SELECT r.*,t.name type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id ORDER BY r.room_number')->fetchAll();
$edit = null;
if (isset($_GET['edit'])) {
    // Load the selected room and populate the form in edit mode.
    $s = $pdo->prepare('SELECT * FROM rooms WHERE id=?');
    $s->execute([$_GET['edit']]);
    $edit = $s->fetch();
} ?>
<!-- Room form and room-type summary -->
<div class="grid-2">
    <section class="panel">
        <h2>
            <?= $edit ? 'Edit room' : 'Add room' ?>
        </h2>
        <form method="post" class="form-grid">
            <input type="hidden" name="id" value="<?=$edit['id'] ?? ''?>">

<label>
            Room number
            <input required name="room_number" value="<?=e($edit['room_number'] ?? '')?>">
            </label>

<label>
            Room type
            <select required name="room_type_id">
                    <?php foreach ($types as $t):?>
                        <option value="<?=$t['id']?>" <?=($edit['room_type_id'] ?? '') == $t['id'] ? 'selected' : ''?>>
                            <?=e($t['name'])?>
                        </option>
                    <?php endforeach;?>
                </select>
            </label>

<label>
            Price per night
            <input required min="0" step="0.01" type="number" name="price" value="<?=e($edit['price'] ?? '')?>">
            </label>

<label>
            Status
            <select name="status">
                    <?php foreach (['Available', 'Booked', 'Under Maintenance'] as $x):?>
                        <option <?=$x == ($edit['status'] ?? 'Available') ? 'selected' : ''?>>
                            <?=$x?>
                        </option>
                    <?php endforeach;?>
                </select>
            </label>

<label class="full">
            Notes
            <input name="notes" value="<?=e($edit['notes'] ?? '')?>">
            </label>
            <button>
                <?= $edit ? 'Save changes' : 'Add room' ?>
            </button>
        </form>
    </section>
    <section class="panel">
        <h2>Room types</h2>
        <p>Default types are imported with the database: Single, Double and Deluxe. Prices can be set for each room above.</p>
        <ul>
            <?php foreach ($types as $t):?>
                <li>
                    <?=e($t['name'])?> - $<?=number_format($t['base_price'], 2)?>
                </li>
            <?php endforeach;?>
        </ul>
    </section>
</div>
<!-- Complete room list -->
<section class="panel">
    <h2>All rooms</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Type</th>
                <th>Price/night</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $r):?>
                <tr>
                    <td>
                        <?=e($r['room_number'])?>
                    </td>
                    <td>
                        <?=e($r['type_name'])?>
                    </td>
                    <td>$<?=number_format($r['price'], 2)?>
                    </td>
                    <td>
                        <span class="badge <?=strtolower(str_replace(' ', '-', $r['status']))?>">
                            <?=e($r['status'])?>
                        </span>
                    </td>
                    <td>
                        <a href="rooms.php?edit=<?=$r['id']?>">Edit</a>
                        <form class="inline" method="post">
                            <input type="hidden" name="id" value="<?=$r['id']?>">
                            <button class="link danger" data-confirm="Delete this room?" name="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</section>
<?php require 'includes/footer.php'; ?>
