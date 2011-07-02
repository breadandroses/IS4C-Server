function show_payment()
{
    $("#add_payment").load('add_payment.php');
    $("#add_payment").animate({opacity: 1}, 'slow');
}

function cancel_payment()
{
    $("#payment").load('payment.php');
}

function confirm_payment()
{
    $("#payment").load("confirm_payment.php");
}
