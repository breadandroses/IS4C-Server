<?php

$mysql_connection = mysql_connect("localhost", "root");

if (!$mysql_connection) {
    echo "SQL Connection Failed<br /> " . mysql_error();
    exit;
}

if (!mysql_select_db("time")) {
    echo "Unable to open time schema<br /> " . mysql_error();
    exit;
}

function get_shifts($day, $month, $year)
{
    $day = mysql_real_escape_string($day);
    $month = mysql_real_escape_string($month);
    $year = mysql_real_escape_string($year);
    $count = 0;
    $sql_query = '
        SELECT shift_id,
            start,
            end
            FROM shifts
            WHERE start BETWEEN "' . $year . '-' . $month . '-' . $day . ' 00:00:00" AND "' . $year . '-' . $month . '-' . $day . ' 23:59:59"
            ORDER BY start;';
    $shift_results = mysql_query($sql_query);

    $shift_list = array();
    while ($row = mysql_fetch_assoc($shift_results))
    {
        $count++;
        $shift_list[$count]['shift_id'] = $row['shift_id'];
        $shift_list[$count]['start'] = $row['start'];
        $shift_list[$count]['end'] =  $row['end'];
    }
    return $shift_list;
}

function insert_shift($shift_start, $shift_end)
{
    $shift_start = mysql_real_escape_string($shift_start);
    $shift_end = mysql_real_escape_string($shift_end);
    if ($shift_start < $shift_end)
    {
        $sql_query = '
            INSERT INTO shifts
                (
                    start,
                    end
                )
                VALUES
                (
                    FROM_UNIXTIME(' . $shift_start . '),
                    FROM_UNIXTIME(' . $shift_end . ')
                );';
        mysql_query($sql_query);
        return true;
    }
    else
    {
        return false;
    }
}

function update_shift($shift_id, $shift_start, $shift_end)
{
    $shift_id = mysql_real_escape_string($shift_id);
    $shift_start = mysql_real_escape_string($shift_start);
    $shift_end = mysql_real_escape_string($shift_end);
    $sql_query = '
        UPDATE shifts
            SET start = ' . $shift_start . ',
                end = ' . $shift_end . '
            WHERE shift_id = ' . $shift_id . ';';
    mysql_query($sql_query);
    return true;
}

function delete_shift($shift_id)
{
    $shift_id = mysql_real_escape_string($shift_id);
    $sql_query = '
        DELETE FROM shifts
            WHERE shift_id = ' . $shift_id . ';';
    mysql_query($sql_query);
    return true;
}

function get_shift_workers($shift_id)
{
    $shift_id = mysql_real_escape_string($shift_id);
    $count = 0;
    $sql_query = '
        SELECT emp_no,
            FirstName,
            LastName
            FROM is4c_op.employees
                JOIN employee_shifts
                    USING (emp_no)
            WHERE shift_id = ' . $shift_id . ';';
    $worker_results = mysql_query($sql_query);

    $worker_list = array();
    while ($row = mysql_fetch_assoc($worker_results))
    {
        $count++;
        $worker_list[$count]['emp_no'] = $row['emp_no'];
        $worker_list[$count]['first_name'] =  $row['FirstName'];
        $worker_list[$count]['last_name'] =  $row['LastName'];
        $worker_list[$count]['worked'] = check_shift_worked($row['emp_no'], $shift_id);
    }
    return $worker_list;
}

function add_shift_worker($emp_id, $shift_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $shift_id = mysql_real_escape_string($shift_id);
    $sql_query = '
        INSERT INTO employee_shifts
        (
            emp_no,
            shift_id
        )
            VALUES
            (
                ' . $emp_id . ',
                ' . $shift_id . '
            );';
    mysql_query($sql_query);
    return true;
}

function delete_shift_worker($emp_id, $shift_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $shift_id = mysql_real_escape_string($shift_id);
    $sql_query = '
        DELETE FROM employee_shifts
        WHERE emp_no = ' . $emp_id . '
            AND shift_id = ' . $shift_id . ';';
    mysql_query($sql_query);
    return true;
}

function get_active_employees()
{
    $count = 0;
    $sql_query = '
        SELECT emp_no,
            FirstName,
            LastName
            FROM is4c_op.employees
            WHERE EmpActive = 1
            ORDER BY LastName,
                FirstName;';
    $employee_results = mysql_query($sql_query);

    $employee_list = array();
    while ($row = mysql_fetch_assoc($employee_results))
    {
        $count++;
        $employee_list[$count]['emp_no'] = $row['emp_no'];
        $employee_list[$count]['first_name'] =  $row['FirstName'];
        $employee_list[$count]['last_name'] =  $row['LastName'];
    }
    return $employee_list;
}

function mark_shift_worked($emp_id, $shift_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $shift_id = mysql_real_escape_string($shift_id);
    $sql_query = '
        INSERT INTO volunteer_hours
            (
                emp_no,
                shift_id
            )
            VALUES
            (
                ' . $emp_id . ',
                ' . $shift_id . '
            );';
    mysql_query($sql_query);
    return true;
}

function unmark_shift_worked($emp_id, $shift_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $shift_id = mysql_real_escape_string($shift_id);
    $sql_query = '
        DELETE FROM volunteer_hours
        WHERE emp_no = ' . $emp_id . '
            AND shift_id = ' . $shift_id . ';';
    mysql_query($sql_query);
    return true;
}

function check_shift_worked($emp_id, $shift_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $shift_id = mysql_real_escape_string($shift_id);
    $count = 0;
    $sql_query = '
        SELECT id
            FROM time.volunteer_hours
            WHERE shift_id = ' . $shift_id . '
                AND emp_no = ' . $emp_id . ';';
    $worked_results = mysql_query($sql_query);

    while ($row = mysql_fetch_assoc($worked_results))
    {
        $count++;
    }
    if ($count > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function get_employee_information($emp_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $sql_query = '
        SELECT employees.FirstName,
	        employees.LastName,
	        employees.jobTitle,
	        employees.EmpActive,
	        employees.email,
            custdata.memType,
            UNIX_TIMESTAMP(custdata.orientationDate) orientationDate
	        FROM is4c_op.employees
                LEFT JOIN is4c_op.custdata
                    ON emp_no = CardNo
	        WHERE emp_no = ' . $emp_id . ';';

    $employee_results = mysql_query($sql_query);

    $employee_information = array();
    while ($row = mysql_fetch_assoc($employee_results))
    {
        $employee_information['first_name'] = $row['FirstName'];
        $employee_information['last_name'] = $row['LastName'];
        $employee_information['email'] = $row['email'];
        $employee_information['active'] = $row['EmpActive'];
        $employee_information['type'] = $row['memType'];
        $employee_information['orientation_date'] = new DateTime(date('Y-m-d', $row['orientationDate']));
    }

    return $employee_information;

}

function get_hours_worked($emp_id, $days)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $days = mysql_real_escape_string($days);
    $count = 0;
    $sql_query = '
        SELECT emp_no,
          FirstName,
          LastName,
          SUM(UNIX_TIMESTAMP(shifts.end) - UNIX_TIMESTAMP(shifts.start)) / 3600 "hours_worked"
          FROM is4c_op.employees
            LEFT JOIN time.employee_shifts
              USING (emp_no)
            LEFT JOIN time.shifts
              USING (shift_id)
            LEFT JOIN time.volunteer_hours
              USING (shift_id, emp_no)
          WHERE shifts.start > NOW() - INTERVAL ' . $days . ' DAY
            AND emp_no = ' . $emp_id . '
          GROUP BY emp_no,
            FirstName,
            LastName;';

    $worked_hours = mysql_query($sql_query);

    while ($row = mysql_fetch_assoc($worked_hours))
    {
        $count++;
        $amount = $row['hours_worked'];
    }

    if ($count == 0)
    {
        return 0;
    }
    else
    {
        return $amount;
    }
}

function get_employees($status = null)
{
    $count = 0;
    $status = mysql_real_escape_string($status);
    $sql_query = '
        SELECT CardNo
	        FROM is4c_op.custdata
	        WHERE CardNo > 0';
	if ($status != null && $status != 3)
	{
	    $sql_query .= ' AND memType = ' . $status . ';';
	}
        elseif ($status == 3)
        {
            $sql_query .= ' AND memType != 2;';
        }
	else
	{
	    $sql_query .= ';';
	}

    $employees = mysql_query($sql_query);

    $employee_list = array();
    while ($row = mysql_fetch_assoc($employees))
    {
        $count++;
        $employee_list[$count]['id'] = $row['CardNo'];
    }
    $employee_list['count'] = $count;

    return $employee_list;
}

function add_activation($emp_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $sql_query = '
        UPDATE is4c_op.custdata
            SET memType = 0
            WHERE CardNo = ' . $emp_id . ';';
    mysql_query($sql_query);
    return true;
}

function add_suspension($emp_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $sql_query = '
        UPDATE is4c_op.custdata
            SET memType = 1
            WHERE CardNo = ' . $emp_id . ';';
    mysql_query($sql_query);
    return true;
}

function add_inactive($emp_id)
{
    $emp_id = mysql_real_escape_string($emp_id);
    $sql_query = '
        UPDATE is4c_op.custdata
            SET memType = 2
            WHERE CardNo = ' . $emp_id . ';';
    mysql_query($sql_query);
    return true;
}

function get_employee_shifts($emp_id)
{
    $day = mysql_real_escape_string($emp_id);
    $count = 0;
    $sql_query = '
        SELECT shift_id,
	        shifts.start,
	        shifts.end,
	        IF (id IS NULL, 0, 1) worked
	        FROM time.employee_shifts
		        JOIN time.shifts
			        USING (shift_id)
		        LEFT JOIN time.volunteer_hours
			        USING (shift_id)
	        WHERE employee_shifts.emp_no = ' . $emp_id . '
	        ORDER BY start DESC;';
    $shift_results = mysql_query($sql_query);

    $shift_list = array();
    while ($row = mysql_fetch_assoc($shift_results))
    {
        $shift_list[$count]['shift_id'] = $row['shift_id'];
        $shift_list[$count]['start'] = $row['start'];
        $shift_list[$count]['end'] =  $row['end'];
        $shift_list[$count]['worked'] = $row['worked'];
        $count++;
    }
    return $shift_list;
}

