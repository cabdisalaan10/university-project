<?php
// Records payments and shows the payment history with invoice links.
$page_title = 'Payments & Invoices';
require 'includes/header.php';
// Save a payment submitted from the recording form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('INSERT INTO payments(booking_id,amount,payment_method,payment_status,paid_on,notes) VALUES(?,?,?,?,?,?)')->execute([$_POST['booking_id'], $_POST['amount'], $_POST['payment_method'], $_POST['payment_status'], $_POST['paid_on'] ?: null, trim($_POST['notes'])]);
    flash('Payment recorded.');
    redirect('payments.php');
}
// Load eligible bookings and previously recorded payments.
$bookings = $pdo->query("SELECT b.id,b.booking_code,b.total_amount,c.full_name FROM bookings b JOIN customers c ON c.id=b.customer_id WHERE b.status!='Cancelled' ORDER BY b.created_at DESC")->fetchAll();
$payments = $pdo->query("SELECT p.*,b.booking_code,c.full_name FROM payments p JOIN bookings b ON b.id=p.booking_id JOIN customers c ON c.id=b.customer_id ORDER BY p.created_at DESC")->fetchAll(); ?>
<!-- Payment recording form -->
<section class="panel">
    <h2>Record payment</h2>
    <form method="post" class="form-grid">

<label>
            Booking
            <select required name="booking_id">
                <option value="">Select booking</option>
                <?php foreach ($bookings as $b):?>
                    <option value="<?=$b['id']?>">
                        <?=e($b['booking_code'])?> - <?=e($b['full_name'])?> ($<?=number_format($b['total_amount'], 2)?>)</option>
                <?php endforeach;?>
            </select>
        </label>

<label>
            Amount
            <input required step="0.01" min="0" type="number" name="amount">
        </label>

<label>
            Method
            <select name="payment_method">
                <option>Cash</option>
                <option>Card</option>
                <option>Mobile Money</option>
                <option>Bank Transfer</option>
            </select>
        </label>

<label>
            Status
            <select name="payment_status">
                <option>Pending</option>
                <option>Paid</option>
                <option>Completed</option>
            </select>
        </label>

<label>
            Paid on
            <input type="date" name="paid_on" value="<?=date('Y-m-d')?>">
        </label>

<label>
            Notes
            <input name="notes">
        </label>
        <button>Record payment</button>
    </form>
</section>
<!-- Payment history and invoice links -->
<section class="panel">
    <h2>Payment history</h2>
    <table>
        <thead>
            <tr>
                <th>Booking</th>
                <th>Guest</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $p):?>
                <tr>
                    <td>
                        <?=e($p['booking_code'])?>
                    </td>
                    <td>
                        <?=e($p['full_name'])?>
                    </td>
                    <td>$<?=number_format($p['amount'], 2)?>
                    </td>
                    <td>
                        <?=e($p['payment_method'])?>
                    </td>
                    <td>
                        <span class="badge <?=strtolower($p['payment_status'])?>">
                            <?=e($p['payment_status'])?>
                        </span>
                    </td>
                    <td>
                        <?=e($p['paid_on'] ?? '-')?>
                    </td>
                    <td>
                        <a href="invoice.php?id=<?=$p['id']?>">Invoice</a>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</section>
<?php require 'includes/footer.php'; ?>
