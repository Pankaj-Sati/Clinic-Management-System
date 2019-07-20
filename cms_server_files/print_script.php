<script>

	function printDiv() 
	{

	  var divToPrint=document.getElementById('DivIdToPrint');

	  var newWin=window.open('printable_page.php','hi','height=720,width=1200');

	  newWin.document.open();

	  newWin.document.write('<link rel="stylesheet" href="css/styles.css" type="text/css" />'+
							'<body style="background:#fff"><center>'+
							'<img style="height:auto;width:1000" src="images/print_header.JPG"/>'+
							'<div style="width:1000">'+divToPrint.innerHTML+'</div>'+
							'</center><div style="position:absolute; bottom:0;">'+
							'<img style="height:auto;width:1000" src="images/print_footer.JPG"/>'+
							'<center><a class="button" onclick="window.print()">Print</a><center></div>'+
							'</body>');

	  newWin.document.close();

	  

	}
</script>