<?php 
//This file contains all the error messages that will be shown to the client in case of errors
//This file is created to centralize the error messages so that anyone can edit the messages easily from one location

define('DATABASE_CONNECION_ERROR','Failed to connect to database. Please refresh the page. If the problem still persist, contact the system administrator');
define('DATABASE_ACCESS_ERROR','Failed to access data from database. Please refresh the page. If the problem still persist, contact the system administrator');
define('DATABASE_INSERT_ERROR','Failed to insert data into database. Please refresh the page. If the problem still persist, contact the system administrator');
define('DATABASE_UPDATE_ERROR','Failed to update data. Please try again. If the problem still persist, contact the system administrator');
define('DATABASE_DELETE_ERROR','Failed to delete data. Please try again. If the problem still persist, contact the system administrator');
define('DATABASE_UPDATE_SUCCESS','Update Successful');
define('DATABASE_NO_RECORD_ERROR','No record found in the database. Please add some data. If the problem still persist, contact the system administrator');

//---------------------------------Patient Data error-----------------------------//
define('NO_FIRST_NAME_ERROR','First name of the patient not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_LAST_NAME_ERROR','Last name of the patient not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_Y_O_B_ERROR','Year of birth of the patient not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_GENDER_ERROR','Gender of the patient not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_PATIENT_ID_ERROR','Patient ID not received from previous page. If the problem still persist, contact the system administrator');
define('PATIENT_IS_BLACKLIST_COLOR','#3e3e3e');
define('PATIENT_IS_NOT_BLACKLIST_COLOR','#fff'); 

//---------------------------------Checkup Record error-----------------------------//
define('NO_CHECKUP_RECORD_ID_ERROR','Checkup Record ID not received from previous page. If the problem still persist, contact the system administrator');
define('NO_CHECKUP_DATE_ERROR','Checkup date not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_COUNSELOR_ID_ERROR','Counselor details for this checkup not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_CHECKUP_TYPE_ID_ERROR','Checkup type not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_NUM_OF_SESSIONS_ERROR','Number of sessions not received. Please reload the page. If the problem still persist, contact the system administrator');
define('NO_VALID_TILL_DATE_ERROR','Valid till date not received from previous page. If the problem still persist, contact the system administrator');
define('INVALID_VALID_TILL_DATE_ERROR','Valid till date cannot be before date of checkup. Change it again. If the problem still persist, contact the system administrator');
define('CHECKUP_DELETE_ERROR','Cannot delete this checkup because it contains some Invoice data. Please delete all invoice of this checkup. If error still persists, contact the system administrator');


//---------------------------------Checkup Session error-----------------------------//
define('NO_CHECKUP_SESSION_ID_ERROR','No session number received.Please try again. If the problem still persist, contact the system administrator');
define('NO_CHECKUP_SESSION_ERROR','No checkup sessions were found in the database.Please add some. If the problem still persist, contact the system administrator');
define('VALIDITY_EXPIRED_ERROR','Validity of this checkup is expired.You cannot add any session for this checkup. You may update the valid till date of this checkup to proceed. If the problem still persist, contact the system administrator');
define('SESSIONS_EXPIRED_ERROR','All the sessions of this checkup are completed i.e Sessions left=0. Adding another session will only change total sessions. If the problem is unknown, contact the system administrator');
define('NO_SESSION_DATE_ERROR','Session date not received. Please add date with the session. If the problem still persist, contact the system administrator');

//---------------------------------Counselor error-----------------------------//
define('NO_COUNSELOR_ERROR','No Counselor was found in the database. Please add some counselors. If the problem still persist, contact the system administrator');

//---------------------------------Checkup Type error-----------------------------//
define('NO_CHECKUP_TYPE_ERROR','No checkup type was found in the database. Please add some checkup types. If the problem still persist, contact the system administrator');

//---------------------------------Invoice Record error-----------------------------//
define('NO_INVOICE_ID_ERROR','No invoice number received.Please try again. If the problem still persist, contact the system administrator');
define('NO_INVOICE_FOUND_ERROR','No invoice found.Please add an invoice for this checkup. If the problem still persist, contact the system administrator');
define('NO_INVOICE_AMOUNT_ERROR','No invoice amount received.Please add amount to this invoice. If the problem still persist, contact the system administrator');
define('NO_INVOICE_DATE_ERROR','No invoice date received.Please add date to this invoice. If the problem still persist, contact the system administrator');
define('NO_INVOICE_PAYED_BY_ERROR','No payment mode received.Please add payment mode to this invoice. If the problem still persist, contact the system administrator');

//---------------------------------Checkup Expense error-----------------------------//
define('NO_EXPENSE_ID_ERROR','No Expense ID received.Please try again. If the problem still persist, contact the system administrator');
define('NO_EXPENSE_FOUND_ERROR','No EXPENSE found.Please add an EXPENSE. If the problem still persist, contact the system administrator');
define('NO_EXPENSE_NAME_ERROR','No EXPENSE name received.Please add an EXPENSE name like salary, napkin etc. If the problem still persist, contact the system administrator');
define('NO_EXPENSE_PRICE_ERROR','No EXPENSE price received.Please add amount to this expense. If the problem still persist, contact the system administrator');
define('NO_EXPENSE_DATE_ERROR','No EXPENSE date received.Please add date to this EXPENSE. If the problem still persist, contact the system administrator');
define('NO_EXPENSE_QUANTITY_ERROR','No EXPENSE Quantity received.Please add quantity to this EXPENSE. If the problem still persist, contact the system administrator');


//------------------------Other Color combinations------------------//
define('LIST_SEPERATE_COLOR','#272727'); 
?>