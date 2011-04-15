<HTML>
<head>
<title>Shelftag Generator</title>
<link href="../src/style.css" rel="stylesheet" type="text/css" />
<script src="../src/CalendarControl.js" language="javascript"></script>
<script src="../src/putfocus.js" language="javascript"></script>
</head>
<body onLoad='putFocus(0,0);'>
<link href="../src/style.css" rel="stylesheet" type="text/css">
<script src="../src/CalendarControl.js" language="javascript"></script>

<form method='post' action='shelftags.php'>
	
<h2>Shelftag Generator</h2>

<?php
$db = mysql_connect('localhost',$_SESSION["mUser"],$_SESSION["mPass"]);
mysql_select_db('is4c_op', mysql_connect('localhost',$_SESSION["mUser"],$_SESSION["mPass"]));

$query = '
    SELECT dept_no,
        dept_name
        FROM departments
        ORDER BY dept_name;
';

$results = mysql_query($query);

?>

<table border="0" cellspacing="3" cellpadding="3">
	<tr> 
		<th align="center"> <p><b>Select dept.*</b></p></th>
	</tr>
	<tr>
	    <td><font size="-1"><p>
	        <?php
	        while($row = mysql_fetch_array($results))
	        {?>
	            <input type="checkbox" name="dept[]" value="<?=$row['dept_no']?>"><?=$row['dept_name']?><br>
	        <?php
	        }
	        mysql_free_result($results);
	        ?>
	        </p></font>
	    </td>
	</tr>
</table>
<table border="0" cellspacing="3" cellpadding="3">
<tr>
	<td align="right">
		<p><b>Date Start</b> </p>
    	<p><b>End</b></p>
	</td>
	<td>			
		<p><input type=text size=10 name=date1 onfocus="showCalendarControl(this);">&nbsp;&nbsp;*</p>
		<p><input type=text size=10 name=date2 onfocus="showCalendarControl(this);">&nbsp;&nbsp;*</p>
	</td>
	<td colspan=2>
		<p>Date format is YYYY-MM-DD</br>(e.g. 2004-04-01 = April 1, 2004)</p>
	</td>
</tr>
<!--<tr>
	<td align="right">
		<input type="checkbox" name="nodate" value="nodate">
	</td>
	<td>
		<p><b>Entire dept.</b></p>
	</td>
	<td>
		<p>Print tags for entire departments.</p>
	</td>
</tr>
--><tr> 
	<td>&nbsp;</td>
	<td> <input type=submit name=submit value="Submit"> </td>
	<td> <input type=reset name=reset value="Start Over"> </td>
</tr>
</table>	

	

</form>
</body>
</HTML>
