<?php
/*******************************************************************************

    Copyright 2007 Alberta Cooperative Grocery, Portland, Oregon.

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

?>
<script language="javascript" type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
<?php


// A page to view and edit a member's details.

$page_title = 'Edit a Member';
include ('./includes/header.html');

// Check for a valid user ID, through GET or POST.
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) )
{
    // Accessed through view_users.php.
    $id = $_GET['id'];
}
elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) )
{
    // Accessed through form submission.
    $id = $_POST['id'];
}
else
{
    // No valid ID, kill the script.
    echo '<h1 id="mainhead">Page Error</h1>
    <p class="error">This page has been accessed in error.</p>
    <p class="error">You have to <b><a href="auto_mem_find.php">find a member</a></b> before you can edit them.</p><p><br /><br /></p>'; 
    include ('./includes/footer.html');
    exit();
}

require_once($_SERVER["DOCUMENT_ROOT"] . '/src/mysql_connect.php'); // Connect to the database.

if (isset($_POST['submitted']))
{
    // If the form has been submitted, check the new data and update the record.    
    // Initialize the errors array.
    $errors = array();
    
    // Validate the form data.
    if (empty($_POST['first_name']))
    {        
        $errors[] = 'You left their first name blank.';    
    }
    else
    {
        $fn = escape_data($_POST['first_name']); // Store the first name.
    }

    if (empty($_POST['last_name']))
    {
        $errors[] = 'You left their last name blank.';    
    }
    else
    {
        $ln = escape_data($_POST['last_name']); // Store the last name.
    }
    $email = escape_data($_POST['email']);
    $phoneNo = escape_data($_POST['phone_number']);
    $street1 = escape_data($_POST['street1']);
    $street2 = escape_data($_POST['street2']);
    $city = escape_data($_POST['city']);
    $state = escape_data($_POST['state']);
    $zip = escape_data($_POST['zip']);
    $available_sunday = (isset($_POST['available_sunday']) && $_POST['available_sunday'] == 'on'?1:0);
    $available_monday = (isset($_POST['available_monday']) && $_POST['available_monday'] == 'on'?1:0);
    $available_tuesday = (isset($_POST['available_tuesday']) && $_POST['available_tuesday'] == 'on'?1:0);
    $available_wednesday = (isset($_POST['available_wednesday']) && $_POST['available_wednesday'] == 'on'?1:0);
    $available_thursday = (isset($_POST['available_thursday']) && $_POST['available_thursday'] == 'on'?1:0);
    $available_friday = (isset($_POST['available_friday']) && $_POST['available_friday'] == 'on'?1:0);
    $available_saturday = (isset($_POST['available_saturday']) && $_POST['available_saturday'] == 'on'?1:0);
    $experience_org = (isset($_POST['experience_org']) && $_POST['experience_org'] == 'on'?1:0);
    $experience_clean = (isset($_POST['experience_clean']) && $_POST['experience_clean'] == 'on'?1:0);
    $experience_cashier = (isset($_POST['experience_cashier']) && $_POST['experience_cashier'] == 'on'?1:0);
    $experience_phone = (isset($_POST['experience_phone']) && $_POST['experience_phone'] == 'on'?1:0);
    $experience_customer = (isset($_POST['experience_customer']) && $_POST['experience_customer'] == 'on'?1:0);
    $experience_grocery = (isset($_POST['experience_grocery']) && $_POST['experience_grocery'] == 'on'?1:0);
    $experience_paper = (isset($_POST['experience_paper']) && $_POST['experience_paper'] == 'on'?1:0);
    $skills_computer = (isset($_POST['skills_computer']) && $_POST['skills_computer'] == 'on'?1:0);
    $skills_computer_list = escape_data($_POST['skills_computer_list']);
    $skills_database = (isset($_POST['skills_database']) && $_POST['skills_database'] == 'on'?1:0);
    $skills_database_list = escape_data($_POST['skills_database_list']);
    $skills_construction = (isset($_POST['skills_construction']) && $_POST['skills_construction'] == 'on'?1:0);
    $skills_construction_list = escape_data($_POST['skills_construction_list']);
    $skills_truckvan = (isset($_POST['skills_truckvan']) && $_POST['skills_truckvan'] == 'on'?1:0);
    $skills_truckvan_list = escape_data($_POST['skills_truckvan_list']);
    $languages = escape_data($_POST['languages']);

    if ((isset($_POST['charge_ok'])) && ($_POST['charge_ok'] = 'on'))
    {
        $ChargeOk = 1;
    }
    elseif ((isset($_POST['charge_ok'])) && ($_POST['charge_ok'] = 'off'))
    {
        $ChargeOk = 0;
    }
    else
    {
        $ChargeOk = 0;
    }

    if (!isset($_POST['checks_ok']))
    {
        $_POST['checks_ok'] = 'off';
    }

    if ($_POST['discount'] > 20)
    {    
        $errors[] = 'You entered a discount greater than the maximum.';
    }
    else
    {
        $d = escape_data($_POST['discount']); // Store the discount.
    }

    if (isset($_POST['orientation_date']))
    {
        $orientation_date = $_POST['orientation_date'];
    }
    else
    {
        $orientation_date = null;
    }
 
    if (empty($errors))
    {
        if (isset($_POST['staff']) && $_POST['staff'] == 6)
        {
            $Type = 'reg';
        }
        else
        {
            $Type = 'pc';
        }    
        $staff = $_POST['staff'];
        $memtype = $_POST['memtype'];
        if ($_POST['checks_ok'] == 'on')
        {
            $WriteCheck=1;
        }
        else
        {
            $WriteCheck=0;
        }
        $query =
        "    UPDATE custdata
                SET FirstName = '$fn',
                LastName = '$ln',
                email = '$email',
                phoneNo = '$phoneNo',
                WriteChecks = $WriteCheck,
                discount = $d,
                memType = $memtype,
                Type = '$Type',
                staff = '$staff',
                ChargeOk = $ChargeOk,
                addressStreet = '$street1',
                addressStreet2 = '$street2',
                addressCity = '$city',
                addressState = '$state',
                addressZip = '$zip',
                applicationDate = NULL,
                availableSunday = $available_sunday,
                availableMonday = $available_monday,
                availableTuesday = $available_tuesday,
                availableWednesday = $available_wednesday,
                availableThursday = $available_thursday,
                availableFriday = $available_friday,
                availableSaturday = $available_saturday,
                experienceOrganization = $experience_org,
                experienceCleaning = $experience_clean,
                experienceCashier = $experience_cashier,
                experiencePhone = $experience_phone,
                experienceCustomerService = $experience_customer,
                experienceGrocery = $experience_grocery,
                experiencePaperwork = $experience_paper,
                skillsComputers = $skills_computer,
                skillsComputersList = '$skills_computer_list',
                skillsDatabase = $skills_database,
                skillsDatabaseList = '$skills_database_list',
                skillsConstruction = $skills_construction,
                skillsConstructionList = '$skills_construction_list',
                skillsTruckVan = $skills_truckvan,
                skillsTruckVanList = '$skills_truckvan_list',
                languages = '$languages',
                orientationDate = '$orientation_date'
                WHERE id = $id;
        ";
        $result = @mysql_query($query);
            
        if (mysql_affected_rows() == 1)
        { // If the query was successful.                
            echo '<h1 id="mainhead">Edit a Member</h1>
            <p>The member has been edited.</p><p><br /><br /></p>';
        }
        else
        { // The query was unsuccessful.                
            echo '<h1 id="mainhead">System Error</h1>
            <p class="error">There are two possibilities:<br />
            <b>1.)</b> The member could not be edited due to a system error.<br />
            <b>2.)</b> Nothing was changed.</p>';
            echo '<p>' . mysql_error() . '<br /><br />Query: ' . $query . '</p>';
            print_r($_POST);
        }
    }
    else
    { // Report the errors.        
        echo '<h1 id="mainhead">Error!!</h1>
        <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg)
        {
            // Print each error.
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';            
    } // End of if (empty($errors)) IF.

} // End of submit conditional.

// Always show the form.

// Retrieve the user's information.
$query = "SELECT * FROM custdata WHERE id = " . $id;
$query2 = "SELECT staff_no, staff_desc FROM staff ORDER BY staff_no ASC";
$query3 = "SELECT memtype, memDesc FROM memtype ORDER BY memtype ASC";
$result = @mysql_query($query);
$result2 = @mysql_query($query2);
$result3 = @mysql_query($query3);

if (mysql_num_rows($result) == 1)
{
    // Valid id show the form.
    // Get the user's information.
    $row = mysql_fetch_array($result);
    
    // Create the form.
    if ($row["ChargeOk"] == 1)
    {
        $ChargeOk = ' CHECKED';
    }
    else
    {
        $ChargeOk = '';
    }

    if ($row["WriteChecks"] == 1)
    {
        $ChecksOk = ' CHECKED';
    }
    else
    {
        $ChecksOk = '';
    }
    ?>
    <h2>Edit a Member.</h2>
    <form action="auto_mem_edit.php" method="post">
        <p>
            Card Number:
            <?php $CardNo = $row["CardNo"] ?>
            <?=$row["CardNo"]?>
        </p>
            <fieldset name="Contact Information" style="border: 1px; border-color: #000000; border-style: solid">
                <legend>Contact Information</legend>
                <p>
                    First Name:<br/>
                    <input type="text" name="first_name" size="15" maxlength="15" value="<?=$row["FirstName"]?>" required />
                </p>
                <p>
                    Last Name:<br/>
                    <input type="text" name="last_name" size="15" maxlength="30" value="<?=$row["LastName"]?>" required />
                </p>
                <p>
                    E-Mail Address:<br/>
                    <input type="email" name="email" size = "20" maxlength="100" value="<?=$row["email"]?>" />
                </p>
                <p>
                    Phone Number:<br/>
                    <input type="text" name="phone_number" pattern="[1-9][0-9]{2} [0-9+]{3}-[0-9]{4}" placeholder="850 555-1234" size = "15" maxlength="30" value="<?=$row["phoneNo"]?>" />
                </p>
                <p>
                    Orientation Date:<br/>
                    <input type="date" name="orientation_date" value="<?=$row["orientationDate"]?>" />
                </p>
                <p>
                    <fieldset style="border: 1px; border-color: #000000; border-style: dashed;">
                        <legend>Address</legend>
                        <input type="text" name="street1" size = "20" maxlength="50" placeholder="Street Line 1" value="<?=$row["addressStreet"]?>" /><br />
                        <input type="text" name="street2" size = "20" maxlength="50" placeholder="Street Line 2" value="<?=$row["addressStreet2"]?>" /><br />
                        <input type="text" name="city" size = "20" maxlength="20" placeholder="City" value="<?=$row["addressCity"]?>" />
                        <input type="text" name="state" size = "2" maxlength="2" placeholder="FL" value="<?=$row["addressState"]?>" />
                        <input type="text" name="zip" size = "10" maxlength="10" placeholder="Zipcode" value="<?=$row["addressZip"]?>" /><br />
                    </fieldset>
                </p>
            </fieldset>
            <fieldset style="border: 1px; border-color: #000000; border-style: solid">
                <legend>Availability</legend>
                <input type="checkbox" name="available_sunday" id="available_sunday" <?=$row["availableSunday"]?'checked':''?>/> <label for="available_sunday">Sundays</label><br/>
                <input type="text" name"available_sunday_hours" size="20" maxlength="20" placeholder="Available Hours" value="<?=$row["availableSundayHours"]?>" /?><br />
                <input type="checkbox" name="available_monday" id="available_monday" <?=$row["availableMonday"]?'checked':''?>/> <label for="available_monday">Mondays</label><br/>
                <input type="text" name"available_monday_hours" size="20" maxlength="20" placeholder="Available Hours" value="<?=$row["availableMondayHours"]?>" /?><br />
                <input type="checkbox" name="available_tuesday" id="available_tuesday" <?=$row["availableTuesday"]?'checked':''?>/> <label for="available_tuesday">Tuesdays</label><br/>
                <input type="text" name"available_tuesday_hours" size="20" maxlength="20" placeholder="Available Hours" value="<?=$row["availableTuesdayHours"]?>" /?><br />
                <input type="checkbox" name="available_wednesday" id="available_wednesday" <?=$row["availableWednesday"]?'checked':''?>/> <label for="available_wednesday">Wednesdays</label><br/>
                <input type="text" name"available_wednesday_hours" size="20" maxlength="20" placeholder="Available Hours" value="<?=$row["availableWednesdayHours"]?>" /?><br />
                <input type="checkbox" name="available_thursday" id="available_thursday" <?=$row["availableThursday"]?'checked':''?>/> <label for="available_thursday">Thursdays</label><br/>
                <input type="text" name"available_thursday_hours" size="20" maxlength="20" placeholder="Available Hours" value="<?=$row["availableThursdayHours"]?>" /?><br />
                <input type="checkbox" name="available_friday" id="available_friday" <?=$row["availableFriday"]?'checked':''?>/> <label for="available_friday">Fridays</label><br/>
                <input type="text" name"available_friday_hours" size="20" maxlength="20" placeholder="Available Hours" value="<?=$row["availableFridayHours"]?>" /?><br />
                <input type="checkbox" name="available_saturday" id="available_saturday" <?=$row["availableSaturday"]?'checked':''?>/> <label for="available_saturday">Saturdays</label><br/>
                <input type="text" name"available_saturday_hours" size="20" maxlength="20" placeholder="Available Hours" value="<?=$row["availableSaturdayHours"]?>" /?><br />
            </fieldset>
            <fieldset style="border: 1px; border-color: #000000; border-style: solid;">
                <legend>Experience</legend>
                <input type="checkbox" name="experience_org" id="experience_org" <?=$row["experienceOrganization"]?'checked':''?> /><label for="experience_org">Co-op Organization / Team Management Experience</label><br />
                <input type="checkbox" name="experience_clean" id="experience_clean" <?=$row["experienceCleaning"]?'checked':''?> /><label for="experience_clean">Cleaning / Maintenance</label><br />
                <input type="checkbox" name="experience_cashier" id="experience_cashier" <?=$row["experienceCashier"]?'checked':''?> /><label for="experience_cashier">Cashier Experience</label><br />
                <input type="checkbox" name="experience_phone" id="experience_phone" <?=$row["experiencePhone"]?'checked':''?> /><label for="experience_phone">Enjoy Phone Work</label><br />
                <input type="checkbox" name="experience_customer" id="experience_customer" <?=$row["experienceCustomerService"]?'checked':''?> /><label for="experience_customer">Customer Service Experience</label><br />
                <input type="checkbox" name="experience_grocery" id="experience_grocery" <?=$row["experienceGrocery"]?'checked':''?> /><label for="experience_grocery">Grocery Experience</label><br />
                <input type="checkbox" name="experience_paper" id="experience_paper" <?=$row["experiencePaperwork"]?'checked':''?> /><label for="experience_paper">Paper Work / Data Entry</label><br />
            </fieldset>
            <fieldset style="border: 1px; border-color: #000000; border-style: solid;">
                <legend>Skills</legend>
                <input type="checkbox" name="skills_computer" id="skills_computer" <?=$row["skillsComputers"]?'checked':''?> /><label for="skills_computer">Computers</label><br/>
                <input type="text" name="skills_computer_list" size="30" maxlength="30" placeholder="Skills" value="<?=$row["skillsComputersList"]?>" /><br />
                <input type="checkbox" name="skills_database" id="skills_database" <?=$row["skillsDatabase"]?'checked':''?> /><label for="skills_database">Database Programming / Operation</label><br/>
                <input type="text" name="skills_database_list" size="30" maxlength="30" placeholder="Skills" value="<?=$row["skillsDatabaseList"]?>" /><br />
                <input type="checkbox" name="skills_construction" id="skills_construction" <?=$row["skillsConstruction"]?'checked':''?> /><label for="skills_construction">Construction / Electrical / Plumbing</label><br/>
                <input type="text" name="skills_construction_list" size="30" maxlength="30" placeholder="Skills" value="<?=$row["skillsConstructionList"]?>" /><br />
                <input type="checkbox" name="skills_truckvan" id="skills_truckvan" <?=$row["skillsTruckVan"]?'checked':''?> /><label for="skills_truckvan">Own a truck or van</label><br/>
                <input type="text" name="skills_truckvan_list" size="30" maxlength="30" placeholder="Skills" value="<?=$row["skillsTruckVanList"]?>" /><br />
                Languages:<br/>
                <input type="text" name="languages" size="30" maxlength="30" value="<?=$row["languages"]?>" /><br />
            </fieldset>
        </div>

    <?php
    if ($row["staff"] == 1 || $row["staff"] == 2 || $row["staff"] == 5)
    {
        echo '<p>House Charge? <input type="checkbox" name="charge_ok" ' . $ChargeOk . ' /></p>';
    }
    ?>
        <p>
            Write Checks?
            <input type="checkbox" name="checks_ok" <?=$ChecksOk?> />
        </p>
        <p>
            Discount:
            <input type="text" name="discount" size="3" maxlength="2" value="<?=$row["Discount"]?>" />%
        </p>
        <p>
            Member Type:
            <select name="staff">
                <option value='0'>None</option>
    <?php
    while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
    {
        echo '<option value=' . $row2['staff_no'];
        if ($row2['staff_no'] == $row['staff'])
        {
            echo ' selected="selected"';
        }
        echo '>' . $row2['staff_desc'] . '</option>';
    }
    ?>
            </select>
        </p>
        <p>
            Member Status:
            <select name="memtype">
    <?php
    while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
    {
        echo '<option value='. $row3['memtype'];
        if ($row3['memtype'] == $row['memType'])
        {
            echo ' selected="selected"';
        }
        echo '>' . $row3['memDesc'] . '</option>';
    }
    ?>
            </select>
        </p>
        <p>
            <input type="submit" name="submit" value="Submit" />
        </p>
        <input type="hidden" name="submitted" value="TRUE" />
        <input type="hidden" name="id" value="<?=$id?>" />
    </form>

    <!-- This section displays payment  history -->
    <div id="payment"></div>
    <script type='text/javascript'>
        $("#payment").load('payment.php?id=<?=$id?>');
    </script>
    
    <h3>Volunteer History</h3>
    <!-- This section shows the last six shifts assigned to this member -->
    <table border='1'>
        <tr>
            <th>Start</th>
            <th>End</th>
            <th>Worked</th>
        </tr>
    <?php
        $query =
        '
            SELECT time.shifts.start,
                time.shifts.end,
                time.volunteer_hours.id vol_id
                FROM is4c_op.custdata
                    JOIN time.employee_shifts
                        ON (CardNo = emp_no)
                    JOIN time.shifts
                        USING (shift_id)
                    LEFT JOIN time.volunteer_hours
                        USING (shift_id, emp_no)
                WHERE emp_no = ' . $CardNo . '
                ORDER BY time.shifts.start DESC
                LIMIT 6;
        ';

        $result = @mysql_query($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {?>
        <tr>
            <td><?=$row['start']?></td>
            <td><?=$row['end']?></td>
            <td><?=!is_null($row['vol_id'])?'Worked':'Not Worked'?></td>
        </tr>
        <?php
        }
    ?>
    </table>
    <?php
}
else
{
    // Not a valid Member ID
    echo '<h1 id="mainhead">Page Error</h1>
    <p class="error">This page has been accessed in error.</p><p><br /><br /></p>';
}
mysql_close(); // Close the DB connection.
