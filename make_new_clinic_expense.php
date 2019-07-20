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

<h3>Add New Clinic Expense</h3>
<fieldset>

	<legend>Fields marked with * are compulsary</legend>

	<form action="insert_new_checkup_expense.php" method="post">
	<p><label for="name">Name *:</label> <input type="text" maxlength="200" required name="expense_name"/></p>
	<p><label for="name">Price *:</label> <input type="number" min=1 required  placeholder="Enter amount" name="expense_price"/></p>
	<p><label for="name">Quantity *: </label><input type="number" requried min=1  value="1" name="expense_quantity"/></p>
	<p><label for="name">Date *: </label><input type="date" requried value="<?php printf('%s',date('Y-m-d'))?>" name="expense_date"/></p>
	<p><label for="name">Remarks :</label> <input type="text" maxlength="500" name="expense_remarks"/></p>
	
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