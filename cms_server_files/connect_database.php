<?php 

//-------------------------------------------Admin updateable files----------------------------------------------------//
//In order to restrict any user from accessing server pages, we will keep sensitive file here
define('USERNAME','root');
define('SERVER_ADDRESS','localhost');
define('PASSWORD','');
define('DATABASE_NAME','clinic_management_system_db');
function connect_database()
{
	$db=new mysqli(SERVER_ADDRESS,USERNAME,PASSWORD,DATABASE_NAME);
	return $db;
}

?>