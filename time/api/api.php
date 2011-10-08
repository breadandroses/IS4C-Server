<?php

include_once "sql.php";
include_once "swift/swift_required.php";

class shifts
{

    public function shift_list($day, $month, $year)
    {
        return get_shifts($day, $month, $year);
    }

    public function insert_shift($shift_start, $shift_end)
    {
        if ($shift_start >= $shift_end)
        {
            return false;
        }
        else
        {
            return insert_shift($shift_start, $shift_end);
        }
    }

    public function shift_workers($shift_id)
    {
        return get_shift_workers($shift_id);
    }

    public function active_employees()
    {
        return get_active_employees();
    }
}

class employee
{
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $active;
    public $type;
    public $orientation_date;

    function __construct($emp_id)
    {
        $this->id = $emp_id;
        $member_information = get_employee_information($this->id);
        $this->first_name = $member_information['first_name'];
        $this->last_name = $member_information['last_name'];
        $this->email = $member_information['email'];
        $this->active = $member_information['active'];
        $this->type = $member_information['type'];
        $this->orientation_date = $member_information['orientation_date'];
        $this->shifts = get_employee_shifts($this->id);
    }

    public function hoursWorked($days)
    {
        return get_hours_worked($this->id, $days);
    }

    public function activate()
    {
        add_activation($this->id);
    }

    public function suspend()
    {
        add_suspension($this->id);
    }

    public function inactivate()
    {
        add_inactivate($this->id);
    }

    public function email($subject, $message)
    {
        $mail = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('breadandrosesfoodcoop@gmail.com' => 'Bread and Roses'))
            ->setTo($this->email)
            ->setBody($message);

        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
            ->setUsername('breadandrosesfoodcoop@gmail.com')
            ->setPassword('tallahassee');

        $mailer = Swift_Mailer::newInstance($transport);

        $mailer->send($mail);
    }

}

class employee_list
{
    public $active;
    public $suspended;
    public $inactive;
    public $all;

    function __construct()
    {
        $this->active = get_employees(0);
        $this->suspended = get_employees(1);
        $this->inactive = get_employees(2);
        $this->not_inactive = get_employees(3);
        $this->all = get_employees();
    }
}
