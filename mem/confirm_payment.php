<?php
/*******************************************************************************

    This file is part of Fannie.

    IS4C is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    IS4C is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    in the file license.txt along with IS4C; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*********************************************************************************/

require_once($_SERVER["DOCUMENT_ROOT"] . '/src/mysql_connect.php');

if (isset($_GET['id']) && is_numeric($_GET['id']))
{
    $id = $_GET['id'];
}
else
{
    $id = null;
}

if (isset($_GET['date']))
{
    $date = $_GET['date'];
}
else
{
    $date = null;
}

if (isset($_GET['type']))
{
    $type = $_GET['type'];
}
else
{
    $type = null;
}

if (isset($_GET['check']) && is_numeric($_GET['check']))
{
    $check = $_GET['check'];
}
else
{
    $check = 'null';
}

if (isset($_GET['amount']) && is_numeric($_GET['amount']))
{
    $amount = $_GET['amount'];
}
else
{
    $amount = null;
}

$error = null;

if (is_null($id) or is_null($date) or is_null($type) or is_null($amount))
{
    $error = 'Not all parameters specified.';
}

if (strtoupper($type) == 'CHECK' && $check == 'null')
{
    $error = 'No check number specified.';
}

if ($error == null)
{
    $query = "
    INSERT INTO member_payments
        (
            mem_id,
            date,
            type,
            check_number,
            amount
        )
        VALUES
        (
            " . $id . ",
            '" . $date . "',
            '" . $type . "',
            " . $check . ",
            " . $amount . "
        );";

    $result = @mysql_query($query);
    include('/mem/payment.php?id=' . $id);
    require_once($_SERVER["DOCUMENT_ROOT"] . '/mem/payment.php');

    if (mysql_affected_rows() == 0)
    {
        echo 'Error while adding payment. ' . mysql_error();
    }
}
else
{
    require_once($_SERVER["DOCUMENT_ROOT"] . '/mem/payment.php');
    include('/mem/payment.php?id=' . $id);
    echo 'Error: ' . $error;
}
