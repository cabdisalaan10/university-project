<?php
// Provides booking and revenue summaries for the administrator.
$page_title = 'Reports';
require 'includes/header.php';
require_admin();
// Calculate the report summary and revenue grouped by room.
$bookingCount = $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$cancelled = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='Cancelled'")->fetchColumn();
$paymentTotal = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE payment_status IN ('Paid','Completed')")->fetchColumn();
$byRoom = $pdo->query("SELECT r.room_number,COUNT(b.id) bookings,COALESCE(SUM(b.total_amount),0) revenue FROM rooms r LEFT JOIN bookings b ON b.room_id=r.id AND b.status!='Cancelled' GROUP BY r.id ORDER BY revenue DESC")->fetchAll();?>
<!-- Report summary cards -->
<section class="cards">
    <article class="stat blue">
        <small>Total bookings</small>
        <strong>
            <?=$bookingCount?>
        </strong>
    </article>
    <article class="stat orange">
        <small>Cancelled bookings</small>
        <strong>
            <?=$cancelled?>
        </strong>
    </article>
    <article class="stat green">
        <small>Revenue received</small>
        <strong>$<?=number_format($paymentTotal, 2)?>
        </strong>
    </article>
</section>
<!-- Revenue details grouped by room -->
<section class="panel">
    <h2>Revenue by room</h2>
    <table>
        <thead>
            <tr>
                <th>Room</th>
                <th>Bookings</th>
                <th>Booking revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($byRoom as $r):?>
                <tr>
                    <td>
                        <?=e($r['room_number'])?>
                    </td>
                    <td>
                        <?=$r['bookings']?>
                    </td>
                    <td>$<?=number_format($r['revenue'], 2)?>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</section>
<?php require 'includes/footer.php'; ?>
