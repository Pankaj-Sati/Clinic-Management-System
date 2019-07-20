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

<section id="content" >
<article>
<center><h2>Pacific Hospital Management Software</h2></center><br><br>

<h3>Add New Patient</h3>
<fieldset>

	<legend>Fields marked with * are compulsary</legend>

	<form action="insert_new_patient_details.php" method="post">
	<p><label for="name">Name *:</label> <input type="text" required name="patient_name"/></p>
	<p><label for="name">Year Of Birth/ Age *:</label> <input type="number" min=1 required max=2100 placeholder="Age/Y.O.B" name="patient_year_of_birth"/></p>
	<p><label for="name">Gender *:</label> <select required name="patient_gender"><option value="M">Male</option> <option value="F">Female</option> </select></p>
	<p><label for="name">Mobile Number *: </label><input type="number" requried min=1  value="1" name="patient_mobile_number"/></p>
	<p><label for="name">Weight(kgs):</label> <input type="number" step=0.1 max=200 name="patient_weight"/></p>
	<p><label for="name">Height(cm): </label><input type="number" step=0.1 max=300 name="patient_height"/></p>
	<p><label for="name">City: </label><input type="text"  name="patient_city"/></p>
	<p><label for="name">Locality: </label><input type="text"  name="patient_locality"/></p>
	<p><label for="name">Street Address: </label><input type="text"  name="patient_street address"/></p>
	<p><label for="name">Occupation: </label><input type="text"  name="patient_occupation"/></p>
	<p><label for="email">Email ID: </label><input type="email"  name="patient_email_id"/></p>
	<p><label for="name">Blood Group: </label><input type="text"  name="patient_blood_group"/></p>
	<p><label for="name">Is Blacklist: </label><select required name="patient_is_blacklist"><option value="N">NO</option> <option value="Y">YES</option> </select></p>
	<input type="submit" class="formbutton"/>
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