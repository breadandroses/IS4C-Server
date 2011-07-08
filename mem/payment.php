<script language="javascript" type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/payment.js"></script>

<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/src/mysql_connect.php'); // Connect to the database.
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
    $id = $_GET['id'];
}
?>
    <!-- This section displays payment  history -->
    <h3>Payment History</h3>
    <table border='1'>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Check #</th>
            <th>Amount</th>
        </tr>
<?php
$query =
    '
SELECT date,
    type,
    check_number,
    amount
    FROM member_payments
    WHERE mem_id = ' . $id . '
    AND void = FALSE
    ORDER BY date;
    ';
$result = @mysql_query($query);
$payment_total = 0;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
    $payment_total += $row['amount'];
?>
        <tr>
            <td><?=$row['date']?></td>
            <td><?=$row['type']?></td>
            <td><?=$row['check_number']?></td>
            <td><?=$row['amount']?></td>
        </tr>
<?php
}
if ($payment_total == 0)
{?>
        <tr>
            <td colspan='4'>No payments on record</td>
        </tr>
<?php
}
?>
        <tr>
            <td colspan='4'>Total Payment: $<?=number_format($payment_total, 2)?></td>
        </tr>
    </table>
    <div id="add_payment" style="opacity: 1"><span style="opactiy: 1;" onclick="show_payment(<?=$id?>);">Add payment</span></div>
