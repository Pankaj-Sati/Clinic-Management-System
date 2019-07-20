Date.prototype.toShortFormat = function() 
		{

		var month_names =["Jan","Feb","Mar",
						  "Apr","May","Jun",
						  "Jul","Aug","Sep",
						  "Oct","Nov","Dec"];
		
		var day = this.getDate();
		var month_index = this.getMonth();
		var year = this.getFullYear();
		
		return "" + day + "-" + month_names[month_index] + "-" + year;
		}

function printDiv() 
	{

	  var divToPrint=document.getElementById('DivIdToPrint');

	  var newWin=window.open('printable_page.php','Print');

	  newWin.document.open();
		var today = new Date();
	  newWin.document.write('<html><head>'+
							'<link rel="stylesheet" href="css/styles.css" type="text/css" />'+
							'<style>'+
							'#NotToPrint, #NotToPrint *{display:none;}'+
							'@media print {#print_button,#print_button *{visibility: hidden;}'+
							'td {font-size: 20px; color:#111}'+
							'</style>'+
							'</head>'+
							'<body style="background:#fff"><center>'+
							'<img style="height:auto;width:1000" src="images/invoice_header.JPG"/>'+
							'<p align="right" style="font-size: 20px; color:#111">Print Date :'+today.toShortFormat()+'</p>'+
							'<div style="width:1000">'+divToPrint.innerHTML+'</div>'+
							'<hr></center><div style="bottom:0;">'+
							'<img style="height:auto;width:1000" src="images/print_footer.JPG"/>'+
							'<center><a class="button" id="print_button" onclick="window.print()">Print</a><center></div>'+
							'</body>');

	  newWin.document.close();

	  

	}
