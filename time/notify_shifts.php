<?php
include_once "api/swift/swift_required.php";

$mysql_connection = mysql_connect("localhost", "root");
if (!$mysql_connection) {
    echo "SQL Connection Failed<br /> " . mysql_error();
    exit;
}

if (!mysql_select_db("time")) {
    echo "Unable to open time schema<br /> " . mysql_error();
    exit;
}


$notification_days = 2;

$sql_query = "
    SELECT *
        FROM is4c_op.employees
            JOIN time.employee_shifts
                USING (emp_no)
            JOIN time.shifts
                USING (shift_id)
        WHERE email IS NOT NULL
            AND DATE_FORMAT(start, '%Y%m%d') = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL " . $notification_days . " DAY), '%Y%m%d');
    ";

    $shift_results = mysql_query($sql_query);

    $shift_list = array();
    while ($row = mysql_fetch_assoc($shift_results))
    {
        $message = Swift_Message::newInstance()
            ->setSubject('Upcoming shift at Bread and Roses.')
            ->setFrom(array('breadandrosesfoodcoop@gmail.com' => 'Bread and Roses'))
            ->setTo(array($row['email']))
            ->setBody('This is a friendly reminder that your shift at Bread and Roses is coming up.  You are scheduled to volunteer from ' . $row["start"] . ' to ' . $row["end"] . '.  If you have any questions please contact us at (850) 425-8486.  If you wish to unsubscribe from these e-mails, please contact a keyholder.')
            ->addPart('<p>This is a friendly reminder that your shift at <a href="http://www.breadandrosesfoodcoop.com">Bread and Roses</a> is coming up.  You are scheduled to volunteer from ' . $row["start"] . ' to ' . $row["end"] . '.  If you have any questions please contact us at (850) 425-8486.</p>  If you wish to unsubscribe from these e-mails, please contact a keyholder.', 'text/html');

            $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
                ->setUsername('breadandrosesfoodcoop@gmail.com')
                ->setPassword('tallahassee');

            $mailer = Swift_Mailer::newInstance($transport);

            echo $mailer->send($message);
    }
?>
