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
if (isset($_GET['id']) && is_numeric($_GET['id']))
{
    $id = $_GET['id'];
}
?>
<table>
    <tr>
        <th>Date</th>
        <th>Type</th>
        <th>Check #</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td><input id="date" type="date" value="<?=date('Y-m-d')?>" size="10"/></td>
        <td>
            <select id="type">
                <option value="Check">Check</option>
                <option value="Cash">Cash</option>
            </select>
        </td>
        <td><input id="check" type="number" min="0" size="5"/></td>
        <td><input id="amount" type="number" min="0" max="999.99" value="0.00" size="9"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <span style="float: left" onclick="confirm_payment(<?=$id?>, $('#date').val(), $('#type').val(), $('#check').val(), $('#amount').val());">Add</span> <span style="float: right" onclick="cancel_payment()">Cancel</span><br />
        </td>
    </tr>
</table>
