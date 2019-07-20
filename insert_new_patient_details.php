<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/connect_database.php'); //This file contains code to login into the database
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/error_messages.php'); //This file contains error_codes
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/database_schema.php'); //This file contains schema constants of the database tables and columns
require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/data_classes.php'); //This file contains data class definations

$patient_obj=new Patient(); //creating an instance of patient class to add values to members

//------checking the required fields---------//
if(empty($_REQUEST['patient_name']))
{
	printf("%s",NO_FIRST_NAME_ERROR);
	exit;
}
//--------------Extracting first name and last name---------------//
$name=explode(' ',trim($_REQUEST['patient_name']));
	if(count($name)==1)
	{
		$patient_obj->first_name=htmlentities($name[0]);
		$patient_obj->last_name=' '; //Cannot be blank in database
	}
	else if(count($name)>1)
	{
		for($i=0;$i<count($name)-1;$i++)
		{
			if($i==0)
			{
				$patient_obj->first_name=htmlentities($name[$i]);	
			}
			else
			{
				$patient_obj->first_name=$patient_obj->first_name.' '.htmlentities($name[$i]);
			}
			
		}
		$patient_obj->last_name=htmlentities($name[count($name)-1]); //Cannot be blank in database
	}

if(empty($_REQUEST['patient_year_of_birth']))
{
	printf("%s",NO_Y_O_B_ERROR);
	exit;
}
$patient_obj->year_of_birth=intval($_REQUEST['patient_year_of_birth']);

//----------------CHECKING whether this value is an age or Y.O.B----------------------//
if($patient_obj->year_of_birth>0 && $patient_obj->year_of_birth<=200) //0<Age<200 else it is already year of birth
{
	$patient_obj->year_of_birth=intval(date('Y'))-$patient_obj->year_of_birth; //Current Year- age (2018-25)
}


if(empty($_REQUEST['patient_gender']))
{
	printf("%s",NO_GENDER_ERROR);
	exit;
}

if($_REQUEST['patient_gender']=='M')
{
	$patient_obj->gender='MALE';
}
else if($_REQUEST['patient_gender']=='F')
{
	$patient_obj->gender='FEMALE';
}
	

//Following lines will execute only if variable in $_REQUEST[] array are set

//Getting the rest of the variables
if(! empty($_REQUEST['patient_weight']))
{
	$patient_obj->weight=intval($_REQUEST['patient_weight']);
}

if(! empty($_REQUEST['patient_height']))
{
	$patient_obj->height=intval($_REQUEST['patient_height']);
}

if(! empty($_REQUEST['patient_city']))
{
	$patient_obj->city=htmlentities($_REQUEST['patient_city']);
}

if(! empty($_REQUEST['patient_locality']))
{
	$patient_obj->locality=htmlentities($_REQUEST['patient_locality']);
}

if(! empty($_REQUEST['patient_street address']))
{
	$patient_obj->street_address=htmlentities($_REQUEST['patient_street address']);
}

if(! empty($_REQUEST['patient_occupation']))
{
	$patient_obj->occupation=htmlentities($_REQUEST['patient_occupation']);
}

if(! empty($_REQUEST['patient_mobile_number']))
{
	$patient_obj->mobile_number=$_REQUEST['patient_mobile_number'];
}

if(! empty($_REQUEST['patient_email_id']))
{
	$patient_obj->email_id=htmlentities($_REQUEST['patient_email_id']);
}

if(! empty($_REQUEST['patient_blood_group']))
{
	$patient_obj->blood_group=htmlentities($_REQUEST['patient_blood_group']);
}
if(! empty($_REQUEST['patient_is_blacklist']) && strcasecmp($_REQUEST['patient_is_blacklist'],"Y")==0 )
{
	$patient_obj->is_blacklist="Y";
}
else
{
	$patient_obj->is_blacklist="N";
}


//--------------------------------Connecting to the database------------------------------------------//
@$database=connect_database(); //this funtion is defined in another file(connect_database); '@' prevents the php engine to print any error generated after executing this statement

if(mysqli_connect_errno())//This function returns a non zero value if there was any error in connection
{
	 printf("%s",DATABASE_CONNECION_ERROR);
	 exit; //To stop executing the php statements further
}

//The following lines will run only if the connection to database server is successful

$insertQuery="INSERT INTO `".PATIENT_DETAILS_TABLE."`(`".FIRST_NAME."`,`".LAST_NAME."`, `".YEAR_OF_BIRTH."`, `".GENDER."`, `".WEIGHT."`, `".HEIGHT."`, `".CITY."`, `".LOCALITY."`, `".STREET_ADDRESS."`, `".OCCUPATION."`, `".MOBILE_NUMBER."`, `".EMAIL_ID."`, `".BLOOD_GROUP."`,`".IS_BLACKLIST."`)"
			." VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?);";

$statement=$database->prepare($insertQuery); //Prepare a prepared-statement to avoid sql injection attacks
$statement->bind_param('ssdsddssssdsss',
     $patient_obj->first_name,
	 $patient_obj->last_name,
	 $patient_obj->year_of_birth,
	 $patient_obj->gender,
	 $patient_obj->weight,
	 $patient_obj->height,
	 $patient_obj->city,
	 $patient_obj->locality,
	 $patient_obj->street_address,
	 $patient_obj->occupation,
	 $patient_obj->mobile_number,
	 $patient_obj->email_id,
	 $patient_obj->blood_group,  
	 $patient_obj->is_blacklist); //Binding the ? to it's value; s-string 

if(!$statement->execute()) //Actually send the data to the MySQL server
{
	//true means that insert query was successful, false means unsuccessful
	printf('%s',DATABASE_INSERT_ERROR); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
	exit; //Exit executing php further
}

//Following lines will execute only if database insertion was successful
printf("%s","Insert Successful");
if(! $database->insert_id==0) //insert_id is the id generated by an insert/update query of the autoincrement column in the database
{
	header("Location:patient_details.php?p_id=".$database->insert_id);
}
else
{
	//insert_id returns 0 if there is no insert or update query or if the table doesnot have autoincrement column
	header("Location:".$_SERVER['HTTP_REFERER']);
}

?>