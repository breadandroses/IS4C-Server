<?php
include_once "api/api.php";

$go_live = TRUE;
// Number of days to look at hours worked.
$time_frame = 42;
// Number of hours a member must have worked, at least, over the last $time_frame days.
$auto_activate_worked_hours = 7;
$minimum_worked_hours = 4;
$employees = new employee_list();

foreach ($employees->not_inactive as $employee_number)
{
    if (is_array($employee_number) && array_key_exists('id', $employee_number))
    {
        $suspend = TRUE;
        $employee = new employee($employee_number['id']);

        echo $employee->first_name . ' ' . $employee->last_name . '<br/>';

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
                if (array_key_exists(1, $employee->shifts))
                {
                    // Check if member has worked their last two shifts.
                    if (!($employee->shifts[1]['worked'] == 1 && $employee->shifts[0]['worked'] == 1))
                    {
                        if ($employee->shifts[1]['worked'] == 0 && $employee->shifts[0]['worked'] == 1)
                        {
                            if ($employee->hoursWorked($time_frame) >= $minimum_worked_hours)
                            {
                                // Member missed a shift, but has made it up.
                                echo 'Missed a shift at ' . $employee->shifts[1]['start'] . ', but made it up ' . $employee->shifts[0]['start'] . '.';
                                if (mktime(0, 0, 0, substr($employee->shifts[0]['start'], 5, 2), substr($employee->shifts[0]['start'], 8, 2), substr($employee->shifts[0]['start'], 0, 4)) + (7 * 24 * 60 * 60) < mktime())
                                {
                                    $suspend = FALSE;
                                }
                            }
                        }
                    }
                    else
                    {
                        if ($employee->hoursWorked($time_frame) >= $minimum_worked_hours)
                        {
                            echo 'Worked their last two shifts<br/>';
                            $suspend = FALSE;
                        }
                    }
                }
                else
                {
                    if ($employee->shifts[0]['worked'] = 1)
                    {
                        if ($employee->hoursWorked($time_frame) >= $minimum_worked_hours)
                        {
			    echo 'Has only been assigned one shift, and has worked it.<br />';
                            $suspend = FALSE;
                        }
                    }
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
        //$employee->suspend();
    }
    else
    {
        echo 'Is in good standing and has worked ' . $employee->hoursWorked($time_frame) . ' hours.<br/>';
        //$employee->activate();
    }
}
