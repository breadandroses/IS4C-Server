function show_payment(user)
{
    $("#add_payment").load('add_payment.php?id=' + user);
    $("#add_payment").animate({opacity: 1}, 'slow');
}

function cancel_payment(user)
{
    $("#payment").load('payment.php?id=' + user);
}

function confirm_payment(user, date, type, check, amount)
{
    $("#payment").load("confirm_payment.php?id=" + user + "&date=" + date + "&type=" + type + "&check=" + check + "&amount=" + amount);
}
