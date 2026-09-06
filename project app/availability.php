<?php
// Searches rooms that are free for the selected dates and room type.
$page_title = 'Room Availability Search';
require 'includes/header.php';
// Load the room-type options used by the filter.
$types = $pdo->query('SELECT * FROM room_types')->fetchAll();
$results = [];
// Search only after the user supplies a date range.
if (isset($_GET['search'])) {
    $q = "SELECT r.*,t.name type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id WHERE r.status!='Under Maintenance' AND NOT EXISTS (SELECT 1 FROM bookings b WHERE b.room_id=r.id AND b.status IN ('Active','Checked In') AND b.check_in < ? AND b.check_out > ?)";
    $args = [$_GET['check_out'], $_GET['check_in']];
    if ($_GET['type'] !== '') {
        $q .= ' AND r.room_type_id=?';
        $args[] = $_GET['type'];
    }
    $s = $pdo->prepare($q);
    $s->execute($args);
    $results = $s->fetchAll();
} ?>
<!-- Availability search filters -->
<section class="panel">
    <form method="get" class="search">
        <input type="hidden" name="search" value="1">

<label>
            Check in
            <input required type="date" name="check_in" value="<?=e($_GET['check_in'] ?? '')?>">
        </label>

<label>
            Check out
            <input required type="date" name="check_out" value="<?=e($_GET['check_out'] ?? '')?>">
        </label>

<label>
            Room type
            <select name="type">
                <option value="">All types</option>
                <?php foreach ($types as $t):?>
                    <option value="<?=$t['id']?>" <?=($_GET['type'] ?? '') == $t['id'] ? 'selected' : ''?>>
                        <?=e($t['name'])?>
                    </option>
                <?php endforeach;?>
            </select>
        </label>
        <button>Search rooms</button>
    </form>
</section>
<?php if (isset($_GET['search'])):?>
    <section class="panel">
        <h2>Available rooms</h2>
        <div class="room-list">
            <?php foreach ($results as $r):?>
                <article class="room-card">
                    <h3>Room <?=e($r['room_number'])?>
                    </h3>
                    <p>
                        <?=e($r['type_name'])?>
                    </p>
                    <strong>$<?=number_format($r['price'], 2)?> / night</strong>
                    <a class="button" href="bookings.php">Book now</a>
                </article>
            <?php endforeach;?>
            <?php if (!$results):?>
                <p class="empty">No available rooms match your filter.</p>
            <?php endif;?>
        </div>
    </section>
<?php endif;
require 'includes/footer.php'; ?>
