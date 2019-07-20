<?php 

//This files contains data classes which will be used to hold the data from database

class Patient
{
	 public $patient_id;
	 public $first_name;
	 public $last_name;
	 public $year_of_birth;
	 public $gender;
	 public $weight;
	 public $height;
	 public $city;
	 public $locality;
	 public $street_address;
	 public $occupation;
	 public $mobile_number;
	 public $email_id;
	 public $blood_group;
	 public $is_blacklist;

}

class CheckupRecord
{
public $checkup_id;
public $checkup_type; //This value is reference to CheckupType in actual database but in code it will be used to save name of the checkup type
public $counselor_id;
public $date_of_checkup;
public $no_of_sessions;
public $patient_id;
public $remarks;
public $valid_till_date;
public $referred_by;
}

class Counselor
{
public $counselor_id;
public $designation;
public $education;
public $first_name;
public $last_name;
public $registration_number;
public $display_color;
}

class CheckupSession
{
public $checkup_id;
public $date;
public $remarks;
public $session_number;
}

class CheckupType
{
	public $checkup_type_id;
	public $name;
}

class InvoiceRecord
{
public $amount;
public $checkup_id;
public $date;
public $invoice_number;
public $payed_by;
public $remarks;
}

class ClinicExpense
{
public $expense_id;
public $expense_name;
public $price;
public $quantity;
public $date;
public $remarks;
}

?>