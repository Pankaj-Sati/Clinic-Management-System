<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pacific Hospital</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
</head>

<body>

<section id="body" class="width">
		
<!-- ----------------------------Including the sidebar ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/sidebar_navigation.php'); //This file contains code of sidebar ?>

<section id="content" class="column-left" >
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>

<h3>NEW CONSULTATION</h3>
<br>
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
	
//------------------------------Checking whether this page was called for insert or delete-------------------//
	
if(! empty($_POST['checkup_type']))
{
	 //If not empty, we will add it into database
	$checkup_type_obj=new CheckupType();
	$checkup_type_obj->name=htmlentities($_POST['checkup_type']);

	$insertQuery="INSERT INTO `".CHECKUP_TYPE_TABLE."`(`name`) VALUES(?);";
	$statement=$database->prepare($insertQuery); //Prepare a prepared-statement to avoid sql injection attacks
	$statement->bind_param('s',$checkup_type_obj->name); //Binding the ? to it's value; s-string 

	if(!$statement->execute()) //Actually send the data to the MySQL server
	{
		//true means that insert query was successful, false means unsuccessful
		printf('%s',DATABASE_INSERT_ERROR); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
	}
}
	
 else if( !empty($_REQUEST['is_update']) && !empty($_REQUEST['checkup_type_id']) && !empty($_REQUEST['checkup_type_name']))
{
	$id=intval($_REQUEST['checkup_type_id']);
	$name=htmlentities($_REQUEST['checkup_type_name']);
	
	$deleteQuery="UPDATE `checkup_type` SET `name`=? WHERE `checkup_type_id` =?";
	$statement=$database->prepare($deleteQuery); //Prepare a prepared-statement to avoid sql injection attacks
	$statement->bind_param('sd',$name,$id); //Binding the ? to it's value; 

	if(!$statement->execute()) //Actually send the data to the MySQL server
	{
		//true means that insert query was successful, false means unsuccessful
		printf('%s',DATABASE_UPDATE_ERROR); //printf() gives advantage of specifying format specifier to prevent malicious code unlike echo
	}
}



//-------------------------------------Getting list of all the checkup types--------------------------//

$select_query='SELECT `checkup_type_id`, `name` FROM `checkup_type`';
@$statement=$database->prepare($select_query); //This method prepares a statement that can be used later for processing

if( ! $statement->execute()) //The execute method will actually send the query parameters to the MySQL server.
{
	//execute() returns false or failure therefor we checked whether it is false.
	//'!' will make false to true which will execute this if block
	printf("%s","<blockquote><p>".DATABASE_ACCESS_ERROR."</p></blockquote>");
	exit_script();

}

//The following lines will execute only if the query ran successfully on the database server

$checkup_type_obj=new CheckupType();
$statement->bind_result(
	$checkup_type_obj->checkup_type_id,
	 $checkup_type_obj->name);

$statement->store_result(); //Stores all the fetched rows in the statement object
?>
<fieldset><legend>Already in database</legend></fieldset><br>

<?php	
if($statement->num_rows==0) //NUM_ROWS variable of statement class is used to get the number of rows fetched.	
{
	//number of rows ==0 means that there is no record in the database 
	printf("%s","<blockquote><p>".NO_CHECKUP_TYPE_ERROR."</p></blockquote>");
	//Following lines will execute only if there is some data in the database
}
?>
	
	<center>
	<table>
	<tr>
		<th>Type</th>
	</tr>

	<?php
	while($statement->fetch())
	{
		printf("%s","<tr>
		
		<form action='add_checkup_type.php' method='post'>
		<td><input type='text' style='padding:5px;
								color:#333333;
								font-size:17px;
								font-family: \'Source Sans Pro\',\'sans-serif\';
								border:1px solid #ddd;' 
				    name='checkup_type_name' value='".$checkup_type_obj->name."'/></td>
		
		<td> 
			<input type='hidden' name='is_update' value='Y'/>
			<input type='hidden' name='checkup_type_id' value='".$checkup_type_obj->checkup_type_id."'/>		
			<input type='submit' style='border:none;
								background-color: #80B763;
								border-radius: 5px;
								color: #FFFFFF;
								display: inline-block;
								font-weight: bold;
								padding: 8px 10px;
								font-size: 0.8em;
								letter-spacing: 0.25px;
								text-decoration: none;
								text-transform: uppercase;' 
			
						value='Update'/>
		</td>
		</form>
	
		
		</tr>");
		

	}

	?>
	</table>
	</center>
	<br>
	<fieldset>
	<legend>Add new type</legend>
	<form action="add_checkup_type.php" method="post">
		<p><label for="name">Checkup Type *:</label><input type="text" name="checkup_type"/></p>
		<input type="submit" class="formbutton" value="Add"/>
	</form>
	</fieldset>

</article>
	<!-- ----------------------------Including the footer ------------------------------------------- -->
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../cms_server_files/footer.php'); //This file contains code of footer ?>		
</section>
<div class="clear"></div>
</section>
</body>
</html>
