<?php
// Displays a printable invoice for one recorded payment.
$page_title = 'Invoice';
require 'includes/header.php';
// Load the payment and related booking details for the invoice.
$s = $pdo->prepare("SELECT p.*,b.booking_code,b.check_in,b.check_out,b.total_amount,c.full_name,c.phone,c.email,r.room_number FROM payments p JOIN bookings b ON b.id=p.booking_id JOIN customers c ON c.id=b.customer_id JOIN rooms r ON r.id=b.room_id WHERE p.id=?");
$s->execute([$_GET['id'] ?? 0]);
$p = $s->fetch();
if (!$p) {
    // Return to the payment history when the requested invoice does not exist.
    flash('Invoice not found.');
    redirect('payments.php');
}?>
<section class="invoice panel">
    <div class="panel-head">
        <div>
            <div class="brand">Hotel<span>MS</span>
            </div>
            <p>Hotel Booking Management System</p>
        </div>
        <button onclick="window.print()">Print invoice</button>
    </div>
    <hr>
    <div class="invoice-grid">
        <div>
            <h3>Billed to</h3>
            <p>
                <?=e($p['full_name'])?>
                <br>
                <?=e($p['phone'])?>
                <br>
                <?=e($p['email'])?>
            </p>
        </div>
        <div>
            <h3>Invoice details</h3>
            <p>Booking: <?=e($p['booking_code'])?>
                <br>Room: <?=e($p['room_number'])?>
                <br>Paid on: <?=e($p['paid_on'] ?? '-')?>
            </p>
        </div>
    </div>
    <table>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Hotel stay: <?=e($p['check_in'])?> to <?=e($p['check_out'])?>
            </td>
            <td>$<?=number_format($p['amount'], 2)?>
            </td>
        </tr>
        <tr>
            <th>Total paid</th>
            <th>$<?=number_format($p['amount'], 2)?>
            </th>
        </tr>
    </table>
    <p class="invoice-note">Payment method: <?=e($p['payment_method'])?> | Status: <?=e($p['payment_status'])?>
    </p>
</section>
<?php require 'includes/footer.php'; ?>
