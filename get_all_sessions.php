<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />

<script>

	function insert_session(checkup_id)
	{
		var ok=confirm("Add New Session?");
		if(ok)
		{
			document.getElementById('checkup_record_id').value=checkup_id;
			document.getElementById('insert_session_form').submit();
		}
		
	}
</script>

</head>

<body>

		<section id="body" class="width">
		
<!-- ----------------------------Including the sidebar ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sidebar_navigation.php'); //This file contains code of sidebar ?>

<section id="content" class="column-left">
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>
<h3>All Sessions</h3>
<span style="right:0">
		<input type="button" disabled style="width: 16px; height: 16px;border-radius:100%; background-color:#b4ffc5;"/>&nbsp;Active Checkups &nbsp;
			
			<input type="button" disabled style="width: 16px; height: 16px;border-radius:100%; background-color:#ffad9f;"/>&nbsp;Only 1 session left
</span>
<h5>*Note: There can be more than 1 record for same patient because this page shows all sessions in database. So if a patient has taken 3 sessions, there will be 3 entries. </h5>

<?php 

require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations

function exit_script()
{
	//Each time we want the script to stop executing further, we will call this function so that the basic html code is output at the end
	
	?>
	</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
    <?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer 
	?>
	</section>
	
	<div class="clear"></div>
	</section>
    </body>
	</html>

<?php
	exit;
}
@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s","<blockquote><p>".DATABASE_CONNECION_ERROR."</p></blockquote>");
	 exit_script(); //To stop executing the php statements further
}

//The following lines will run only if the connection to database server is successful
?>

<fieldset>
	<form action="get_all_sessions.php" method="post">
		<p><label for="name">Search Patient:</label><input type="text" value="<?php if(! empty($_POST['search_string'])){ printf('%s',$_POST['search_string']);}?>" name="search_string" placeholder="Patient Name or ID" /></p>
		 
		<p><label for="name">Arrange by:</label>
			<input type="radio" name='sort_by' value="sort_date">&nbsp;Session Date</input>
			&nbsp;<input type="radio" name='sort_by' value="sort_session_left">&nbsp;Sessions Left</input>
			&nbsp;<input type="radio" name='sort_by' value="sort_patient">&nbsp;Patients</input></p>
		
		<p><label for="name">Patients Of:</label> <select required name="patients_of">
			<?php

				//-------------------------------------Getting list of all the counselors--------------------------//

			$select_query='SELECT `counselor_id`,`designation`,`first_name`, `last_name` FROM `counselor`';
			@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

			if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
			{
				//execute() returns false or failure therefor we checked whether it is false.
				//'!' will make false to true which will execute this if block
				printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
				exit_script();

			}

			//The following lines will execute only if the query ran successfully on the database server

			$counselor_obj=new Counselor();
			$statement->bind_result(
			$counselor_obj->counselor_id,
			 $counselor_obj->designation,
			 $counselor_obj->first_name,
			 $counselor_obj->last_name );

			$statement->store_result(); //Stores all the fetched rows in the statement object
			if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
			{
				//number of rows ==0 means that there is no record in the database 
				printf("%s","<blockquote><p>".NO_COUNSELOR_ERROR."</p></blockquote>");
				exit_script();
			}

			//Following lines will execute only if there is some data in the database
			if(! empty($_POST['patients_of'])) //Checking whether already searched by doctor
			{
				$doctor_id=intval($_POST['patients_of']);
			}
			else
			{
				$doctor_id=-9999; //imaginary value; not valid	
			}

			printf("%s","<option value=-1");
			if($doctor_id==-1)
			{
				printf("%s"," selected ");
			}
			printf("%s",">All</option>");

			while($statement->fetch())
			{
				printf("%s","<option value=".$counselor_obj->counselor_id);
				if($doctor_id==$counselor_obj->counselor_id)
				{
					printf("%s"," selected ");
				}
				printf("%s",">".$counselor_obj->designation." ".$counselor_obj->first_name.' '.$counselor_obj->last_name."</option>");

			}

			?>
		</select></p>
		
		<input type="submit" class="formbutton" value="Go"/>
			
	
	</form>
</fieldset>
<br>


<?php

if(! empty($_POST['search_string']))
{	
	if(intval(substr($_POST['search_string'],0,1))!=0) //intval will try to convert string into a number if it has any number in it
	{
		//If the first letter in the search string is a number not equal to 0, then we will search by patient ID
		$search_string=' AND PD.`patient_id`='.intval($_POST['search_string']);
		
	}
	else
	{
		$name_array=explode(' ',htmlentities($_POST['search_string'])); //seperating first name and last name if given
		$search_string=' AND PD.`first_name`like \''.$name_array[0]."%'";
	}
	
}
else
{
 $search_string='';
}
//----------Sort records by-----------//
if(! empty($_POST['sort_by']) && strcasecmp($_POST['sort_by'],'sort_session_left')==0)
{
	$sort_by= ' ORDER BY CR.`no_of_sessions` DESC' ;
	$seperate_result=false;
}
else if(! empty($_POST['sort_by']) && strcasecmp($_POST['sort_by'],'sort_patient')==0)
{
	$sort_by= ' ORDER BY PD.`first_name`' ;
	$seperate_result=true;
	$seperate_by_patient=true;
}
else
{
	$sort_by= ' ORDER BY CS.`date` DESC' ;
	$seperate_result=true;
	$seperate_by_patient=false;
}
//----------Patients OF-----------//
if(! empty($_POST['patients_of']))
{
	if(intval($_POST['patients_of'])<0)
	{
		$patient_of_string='';
	}
	else
	{
		$patient_of_string=' AND C.counselor_id='.intval($_POST['patients_of']);
	}
}
else
{
	$patient_of_string='';
}

$select_query='SELECT CR.`checkup_id`,PD.`patient_id`,PD.`first_name`,PD.`last_name`,C.`designation`,C.`first_name`,C.`last_name`, C.`display_color`,PD.`gender`,PD.`year_of_birth`,PD.`is_blacklist`,CR.`date_of_checkup`, CT.`name`, CR.`no_of_sessions`, CR.`valid_till_date`,CS.`session_number`, CS.`date`, CS.`remarks` FROM `checkup_record` AS CR, `patient_details` AS PD, `counselor` AS C, `checkup_type` AS CT, `checkup_session` AS CS WHERE CR.patient_id=PD.patient_id AND CR.counselor_id=C.counselor_id AND CR.checkup_type_id=CT.checkup_type_id AND CR.checkup_id=CS.checkup_id'.$search_string.$patient_of_string.
$sort_by ;

@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
    //'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();
	
}

//The following lines will execute only if the query ran successfully on the database server

$checkup_record_obj=new CheckupRecord();
$patient_obj=new  Patient();
$counselor_obj=new Counselor();
$checkup_session_obj=new CheckupSession();

$statement->bind_result($checkup_record_obj->checkup_id,
$patient_obj->patient_id,
$patient_obj->first_name,
$patient_obj->last_name,
$counselor_obj->designation,
$counselor_obj->first_name,
$counselor_obj->last_name,
$counselor_obj->display_color,
$patient_obj->gender,
$patient_obj->year_of_birth,
$patient_obj->is_blacklist,
$checkup_record_obj->date_of_checkup,
$checkup_record_obj->checkup_type,
$checkup_record_obj->no_of_sessions,
$checkup_record_obj->valid_till_date,
$checkup_session_obj->session_number,
$checkup_session_obj->date,
$checkup_session_obj->remarks
);


$statement->store_result(); //Stores all the fetched rows in the statement object
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
    printf("%s","<blockquote><p>".DATABASE_NO_RECORD_ERROR."</p></blockquote>");
	exit_script();
}

//Following lines will execute only if there is some data in the database
?>
<table>
<tr>

<TH>Serial No.</TH>
<TH>Remarks</TH>

<TH>PATIENT NAME/ID</TH>
<TH>CONSULTANT</TH>
<TH>G/AGE</TH>
<TH>CHECKUP_ID</TH>
<TH>Checkup Type</TH>
<TH>Sessions Left</TH>

</tr>
<?php
$destination_url="patient_details.php";
$temp_value=null;
	$total_session_in_a_date=0; //To show serial number 
while($statement->fetch()) //fetch() will get one database row at a time and bind them into the bind variables
{
	$total_session_in_a_date++; //Increment serial number
	//--------Checking whether to seperated records by date-------------//
	if($seperate_result)
	{
		//If true
		if($seperate_by_patient)
		{
			if($temp_value==null)
			{
				$temp_value=substr($patient_obj->first_name,0,1);//Set the temp_first_character to first record's character
				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><center>$temp_value</center></td></tr>");
			}
			else if(strcasecmp($temp_value,substr($patient_obj->first_name,0,1))!=0)
			{
				$temp_value=substr($patient_obj->first_name,0,1); //Change the temp_first_character
				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><br><hr><center>$temp_value</center></td></tr>");
				$total_session_in_a_date=1; //reset serial number
			}
		}
		else
		{
			if($temp_value==null)
			{
				$temp_value=$checkup_session_obj->date;
				$date=getdate(strtotime($temp_value));

				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><center>".$date['weekday'].", ".$date['mday']." ".$date['month']." ". $date['year']."</center></td></tr>");
			}
			else if(strtotime($temp_value)!=strtotime($checkup_session_obj->date))
			{
				//If both dates are different, then add seperator, and make temp_value the new date
				$temp_value=$checkup_session_obj->date;	
				$date=getdate(strtotime($temp_value));

				printf("%s","<tr><td colspan=8 style='color:".LIST_SEPERATE_COLOR."'><br><hr><center>".$date['weekday'].", ".$date['mday']." ".$date['month']." ". $date['year']."</center></td></tr>");
				$total_session_in_a_date=1; //reset serial number
			}
		}
	}
	
	
	//--------Checking whether the session left is not the last one-------------//
	if($checkup_record_obj->no_of_sessions>1)
	{
		$tr_color='style="background-color: #b4ffc5"';
	}
	else if($checkup_record_obj->no_of_sessions==1)
	{
		$tr_color='style="background-color: #ffad9f"';
	}
	else
	{
		$tr_color='';
	}
	
	printf("%s","<tr>");
	 printf("%s","<td>".$total_session_in_a_date."</td>");
	 printf("%s","<td>".$checkup_session_obj->remarks."</td>");
	
	printf("%s","<td ");
		if(strcasecmp($patient_obj->is_blacklist,"Y")==0)
		{
			printf("%s", "style=\"color:".PATIENT_IS_NOT_BLACKLIST_COLOR.";background-color:".PATIENT_IS_BLACKLIST_COLOR."\"");
		}
		printf("%s","><a href='".$destination_url."?p_id=".$patient_obj->patient_id."'>");
		 printf("%s",$patient_obj->first_name." ".$patient_obj->last_name." / ".$patient_obj->patient_id."</a></td>");
	
	 printf("%s","<td style='background-color:".$counselor_obj->display_color."'>".$counselor_obj->designation." ".$counselor_obj->first_name." ".$counselor_obj->last_name."</td>");
	 printf("%s","<td>".substr($patient_obj->gender,0,1)." /".(intval(date('Y'))-intval($patient_obj->year_of_birth))."</td>");
	 printf("%s","<td><a href='get_checkup_sessions.php?cr_id=".$checkup_record_obj->checkup_id."'>".$checkup_record_obj->checkup_id."</a></td>");
	 printf("%s","<td>".$checkup_record_obj->checkup_type."</td>");
	
	if($checkup_record_obj->no_of_sessions<=0)
	{
		printf("%s","<td $tr_color>".$checkup_record_obj->no_of_sessions."</td>");
	}
	else
	{
		printf("%s","<td $tr_color>".$checkup_record_obj->no_of_sessions);
	
?>
		 &nbsp;
	   <input type="button" value="+" style="width: 20px;text-align: right height: 20px;border-radius:100%; background-color:#FFFFFF;" onClick="insert_session(<?php printf('%s',$checkup_record_obj->checkup_id); ?>)"/><span style="font-size:10px; text-align: right" >&nbsp;NEW</span>
	   
		   
<?php 
		printf("%s","</td>");
	}
	printf("%s","<td><a href='update_checkup_record.php?cr_id=".$checkup_record_obj->checkup_id."'>EDIT</a></td>");
	printf("%s","</tr>");
	
}
?>

</table>
<form action="insert_new_checkup_session.php" id="insert_session_form" method="post">
	<input type="hidden" id="checkup_record_id" name="cr_id" value="" />
	<input type="hidden" value="<?php printf('%s',date('Y-m-d'))?>" required name="session_date"/></p>
</form>

<br>
<a  class="button" onclick="window.print()">Print page</a>
<a href="make_new_patient_details.php" class="button">Add new Patient </a>
<a href="get_all_patient_details.php" class="button">Get all Patients</a>
</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>