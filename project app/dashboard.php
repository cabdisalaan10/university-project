<?php
// Shows the main operational statistics and the latest bookings.
$page_title = 'Dashboard';
require 'includes/header.php';
// Calculate room, booking, payment, chart, and recent-activity data.
$totalRooms = $pdo->query('SELECT COUNT(*) FROM rooms')->fetchColumn();
$available = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Available'")->fetchColumn();
$active = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status IN ('Active','Checked In')")->fetchColumn();
$revenue = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE payment_status IN ('Paid','Completed')")->fetchColumn();
$revenueByMonth = $pdo->query("SELECT DATE_FORMAT(COALESCE(paid_on, created_at), '%b') AS month, COALESCE(SUM(amount), 0) AS total FROM payments WHERE payment_status IN ('Paid','Completed') GROUP BY YEAR(COALESCE(paid_on, created_at)), MONTH(COALESCE(paid_on, created_at)) ORDER BY COALESCE(paid_on, created_at) DESC LIMIT 6")->fetchAll();
$maxChartValue = max(array_column($revenueByMonth, 'total') ?: [1]);
$recent = $pdo->query("SELECT b.*,c.full_name,r.room_number FROM bookings b JOIN customers c ON c.id=b.customer_id JOIN rooms r ON r.id=b.room_id ORDER BY b.created_at DESC LIMIT 5")->fetchAll(); ?>
<?php $cards = [['Total rooms', $totalRooms, 'blue'], ['Available rooms', $available, 'green'], ['Active bookings', $active, 'orange'], ['Payments received', '$' . number_format($revenue, 2), 'purple']]; ?>
<!-- Dashboard statistic cards -->
<section class="cards">
    <?php foreach ($cards as $c):?>
        <article class="stat <?=$c[2]?>">
            <small>
                <?=$c[0]?>
            </small>
            <strong>
                <?=$c[1]?>
            </strong>
        </article>
    <?php endforeach;?>
</section>

<!-- Latest booking activity -->
<section class="panel">
    <div class="panel-head">
        <h2>Recent bookings</h2>
        <a class="button secondary" href="bookings.php">Manage bookings</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Dates</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent as $b):?>
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
                        <?=e($b['check_in'])?> - <?=e($b['check_out'])?>
                    </td>
                    <td>
                        <span class="badge <?=strtolower(str_replace(' ', '-', $b['status']))?>">
                            <?=e($b['status'])?>
                        </span>
                    </td>
                </tr>
            <?php endforeach;?>
            <?php if (!$recent):?>
                <tr>
                    <td colspan="5" class="empty">No bookings yet.</td>
                </tr>
            <?php endif;?>
        </tbody>
    </table>
</section>
<?php require 'includes/footer.php'; ?>
