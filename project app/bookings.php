<?php
// Creates, updates, and cancels hotel room bookings.
$page_title = 'Booking Management';
require 'includes/header.php';
// Handle booking creation, status updates, and cancellation actions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        // Cancel the booking and make its room available again.
        $pdo->prepare("UPDATE bookings b JOIN rooms r ON r.id=b.room_id SET b.status='Cancelled',r.status='Available' WHERE b.id=?")->execute([$_POST['id']]);
        flash('Booking cancelled.');
    } elseif (isset($_POST['update_status'])) {
        // Update the booking status and release the room when appropriate.
        $pdo->prepare('UPDATE bookings SET status=? WHERE id=?')->execute([$_POST['status'], $_POST['id']]);
        if (in_array($_POST['status'], ['Checked Out', 'Cancelled'])) {
            $pdo->prepare("UPDATE rooms r JOIN bookings b ON b.room_id=r.id SET r.status='Available' WHERE b.id=?")->execute([$_POST['id']]);
        }
        flash('Booking status updated.');
    } else {
        $room = $pdo->prepare('SELECT price,status FROM rooms WHERE id=?');
        $room->execute([$_POST['room_id']]);
        $r = $room->fetch();
        $customerId = (int)($_POST['customer_id'] ?? 0);
        if (!$customerId) {
            $pdo->prepare('INSERT INTO customers(full_name,phone,email,national_id,address) VALUES(?,?,?,?,?)')->execute([trim($_POST['full_name']), trim($_POST['phone']), trim($_POST['email']), trim($_POST['national_id']), trim($_POST['address'])]);
            $customerId = $pdo->lastInsertId();
        }
        $nights = max(1, (strtotime($_POST['check_out']) - strtotime($_POST['check_in'])) / 86400);
        $total = $nights * $r['price'];
        $code = 'BK-' . date('Ymd') . '-' . random_int(100, 999);
        $pdo->prepare('INSERT INTO bookings(booking_code,customer_id,room_id,check_in,check_out,adults,children,total_amount,status) VALUES(?,?,?,?,?,?,?,?,?)')->execute([$code, $customerId, $_POST['room_id'], $_POST['check_in'], $_POST['check_out'], $_POST['adults'], $_POST['children'], $total, 'Active']);
        $pdo->prepare("UPDATE rooms SET status='Booked' WHERE id=?")->execute([$_POST['room_id']]);
        flash('Booking created: ' . $code);
    }
    redirect('bookings.php');
}
// Load available rooms and the complete booking history for the page.
$rooms = $pdo->query("SELECT r.*,t.name type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id WHERE r.status='Available' ORDER BY r.room_number")->fetchAll();
$bookings = $pdo->query("SELECT b.*,c.full_name,r.room_number FROM bookings b JOIN customers c ON c.id=b.customer_id JOIN rooms r ON r.id=b.room_id ORDER BY b.created_at DESC")->fetchAll(); ?>
<!-- Booking creation form -->
<section class="panel">
    <h2>New booking</h2>
    <form method="post" class="form-grid">

<label>
            Available room
            <select required name="room_id">
                <option value="">Select room</option>
                <?php foreach ($rooms as $r):?>
                    <option value="<?=$r['id']?>">Room <?=e($r['room_number'])?> - <?=e($r['type_name'])?> ($<?=number_format($r['price'], 2)?>)</option>
                <?php endforeach;?>
            </select>
        </label>

<label>
            Guest name
            <input required name="full_name">
        </label>

<label>
            Phone
            <input required name="phone">
        </label>

<label>
            Email
            <input type="email" name="email">
        </label>

<label>
            National ID
            <input name="national_id">
        </label>

<label>
            Address
            <input name="address">
        </label>

<label>
            Check in
            <input required type="date" min="<?=date('Y-m-d')?>" name="check_in">
        </label>

<label>
            Check out
            <input required type="date" min="<?=date('Y-m-d', strtotime('+1 day'))?>" name="check_out">
        </label>

<label>
            Adults
            <input required type="number" min="1" value="1" name="adults">
        </label>

<label>
            Children
            <input required type="number" min="0" value="0" name="children">
        </label>
        <button>Create booking</button>
    </form>
</section>
<!-- Current and previous booking records -->
<section class="panel">
    <h2>Current and previous bookings</h2>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Check in/out</th>
                <th>Total</th>
                <th>Status</th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b):?>
                <tr>
                    <td>
                        <?=e($b['booking_code'])?>
                    </td>
                    <td>
                        <?=e($b['full_name'])?>
                    </td>
                    <td>
                        <?=e($b['room_number'])?>
                    </td>
                    <td>
                        <?=e($b['check_in'])?>
                        <br>
                        <?=e($b['check_out'])?>
                    </td>
                    <td>$<?=number_format($b['total_amount'], 2)?>
                    </td>
                    <td>
                        <span class="badge <?=strtolower(str_replace(' ', '-', $b['status']))?>">
                            <?=e($b['status'])?>
                        </span>
                    </td>
                    <td>
                        <?php if (in_array($b['status'], ['Active', 'Checked In'])):?>
                            <form method="post" class="inline">
                                <input type="hidden" name="id" value="<?=$b['id']?>">
                                <select name="status">
                                    <option>Active</option>
                                    <option>Checked In</option>
                                    <option>Checked Out</option>
                                </select>
                                <button class="link" name="update_status">Update</button>
                            </form>
                            <form method="post" class="inline">
                                <input type="hidden" name="id" value="<?=$b['id']?>">
                                <button class="link danger" name="cancel" data-confirm="Cancel this booking?">Cancel</button>
                            </form>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</section>
<?php require 'includes/footer.php'; ?>
