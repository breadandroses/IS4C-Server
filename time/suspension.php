<?php
include_once "api/api.php";

$go_live = TRUE;
// Number of days to look at hours worked.
$time_frame = 42;
// Number of hours a member must have worked, at least, over the last $time_frame days.
$auto_activate_worked_hours = 7;
$minimum_worked_hours = 3;
$employees = new employee_list();

foreach ($employees->not_inactive as $employee_number)
{
    if (is_array($employee_number) && array_key_exists('id', $employee_number))
    {
        $suspend = TRUE;
        $employee = new employee($employee_number['id']);

        echo $employee->first_name . ' ' . $employee->last_name . '<br/>';

        // Find the last shift that is before the present date.
        for ($i = 0; $i++; $i < 50)
        {
            $time = mktime(substr($employee->shifts[$i]['start'], 11, 2), substr($employee->shifts[$i]['start'], 14, 2), substr($employee->shifts[$i]['start'], 17, 2), substr($employee->shifts[$i]['start'], 5, 2), substr($employee->shifts[$i]['start'], 8, 2), substr($employee->shifts[$i]['start'], 0, 4));
            if ($time < time())
            {
                $i = 50;
            }
        }

        // Check that enough hours have been worked.
        if ($employee->hoursWorked($time_frame) >= $auto_activate_worked_hours)
        {
            echo 'Has worked at least ' . $auto_activate_worked_hours . ' hours.<br/>';
            $suspend = FALSE;
        }
        else
        {
            //Member has not worked enough hours to automatically keep membership.

            // Check that shifts have been worked appropriately.
            if (array_key_exists(0, $employee->shifts))
            {
                if ($employee->hoursWorked($time_frame) >= $minimum_worked_hours)
                {
                    echo 'Has worked a shift and minimum hours. ';
                    $suspend = FALSE;
                }

                if ($employee->shifts[$i]['worked'] == 0 && $time > time() - 10 * 24 * 60 * 60)
                {
                    echo 'Missed their last shift on ' . $employee->shifts[$i]['start'] . '. ';
                    $suspend = TRUE;
                }
            }
            else
            {
                // Member has never been assigned a shift.  They are fine.
                echo 'Has never been assigned a shift.<br/>';
                $suspend = FALSE;
            }
        }
    }

    // Suspend or active members as appropriate.
    if ($suspend)
    {
        echo 'Is not in good standing.&nbsp;&nbsp;' . $employee->hoursWorked($time_frame) . ' hours have been worked<br/>';
        if ($employee->type == 0)
        {
            echo 'Will now be suspended.<br/>';
            $employee->suspend();
            if ($go_live)
            {
                $employee->email('Suspension notice', 'This is a notice that your membership with Bread and Roses has been suspended.  Oh No!  Why did that happen‽  This is usually due to a lack of volunteer hours or a missed shift.  Please contact Bread & Roses and talk with a key holder to schedule a shift and become an active member again.  Without our dedicated volunteers Bread & Roses would not exist.  We look forward to seeing you reactivated soon.  Our phone number is 850 425-8486.');
            }
        }
        else
        {
            echo 'Already suspended.<br/>';
        }
    }
    else
    {
        echo 'Is in good standing and has worked ' . $employee->hoursWorked($time_frame) . ' hours.<br/>';
        if ($employee->type == 1)
        {
            echo 'Will now be activated<br/>';
            $employee->activate();
            if ($go_live)
            {
                $employee->email('Activation notice', 'This is a notice that your membership with Bread and Roses has been activated effective immediately.  Thank you for continuing to support your local member-owned food cooperative.');
            }
        }
        else
        {
            echo 'Already active.<br/>';
        }
    }
}
