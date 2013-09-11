<?

// --------------------TO DO--------------------
// [TECH] 	- Submit an invoice successfully to database
// [TECH] 	- ERROR CHECK: if NO name is present OR if nothing has been purchased
// [TECH] 	- Submit a draft successfully to database (un-paid)
// [TECH] 	- Create confirmation when invoice is submitted (Prompt: Print invoice / Create another invoice )
// [TECH] 	- Create printable invoice (seperate link) which fits A4 page
// [TECH] 	- Auto-complete search for either First or Surname from database (typeahead.js w/ JSON results from SQLite)
// [BUG] 	- Limit the QTY field to 1 whole digit
// [BUG] 	- Check if QTY field is blank, if so: clear the TOTAL field
// [BUG] 	- Restrict numerical fields to only accept digits
// [UI] 	- Increase font size for watermark
// [UI]		- Lay out the following fields: 'Sub Total' , 'GST' , 'Total'
// [UI]		- When submitting, prompt for 'Has this invoice been paid yet?' (slide down animation)
// [UI]		- Create flat-style button for submitting the invoice
// [UI]		- Lay out 'Adjust your stock' button at top-right of the page
// [UI]		- Light-grey hightlight when hovering over stock items
// [TECH]	- Ensure IE10+ compatibility
// ---------------------------------------------

date_default_timezone_set('Australia/Perth');
$TodayDate = date('d/m/Y');
$CounterDisplay = 0;
$CounterStart = 0;

$db = new PDO('sqlite:carldenn.sqlite') or die("Oh no, cannot connect to database!");
$result = $db->query("SELECT * FROM Products WHERE IsActive='TRUE'");

foreach($result as $row){
	$ProductID[$CounterStart] = $row['ProductID'];
	$DisplayName[$CounterStart] = $row['DisplayName'];
	$SellPrice[$CounterStart] = $row['SellPrice'];
	$CounterStart++;
}

?>
<html>
<head>

<title>Carldenn Wines</title>
<link rel="stylesheet" href="css/style.css" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/datepicker/flat.css" type='text/css'>
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/jquery.watermark.min.js"></script>

<script>

$(function() {
	$("#DateDisplay").datepicker({ 
	showOn: "button",
	buttonImage: "img/appbar.calendar.svg",
	buttonImageOnly: true,
	autoSize: true,
	dateFormat: 'dd/mm/yy',
	});
	
	$("#CustomerName").watermark(" Customer's name");
	$("#CustomerEmail").watermark(" Customer's e-mail address");
});

function gather() {
	
	var str = $("form").serialize();
	
	$.ajax({
		url: "submit.php",
		type: "POST",
		data: {input : $("form").serializeArray()},
		success:function(data) {
			console.log(data);
		}
	});
	
};

</script>

</head>

<body id="CreateInvoice">
<form method="post" action="" id="MainForm">

<div id="Content">

<span id="Title">Carldenn Wines</span>
<input type="text" id="DateDisplay" name="Date" value="<? echo $TodayDate; ?>"> 

<input type="text" name="CustomerName" id="CustomerName"> 
<input type="text" name="CustomerEmail" id="CustomerEmail">

<table border=0 id="Stock">

<td class="HeaderDisplayName"></td>
<td class="HeaderQty">QTY</td>
<td class="HeaderSell">SELL</td>
<td class="HeaderTotal">TOTAL</td>

<? 

// Display data fetched from database: Stock Name, Stock ID and Sell Price
foreach($ProductID as $value) {
	echo '
	<tr>
	<td class="DisplayName"> '. $DisplayName[$CounterDisplay] .' </td>
	<td> <input type="text" class="Quantity" name="Qty_'.$ProductID[$CounterDisplay].'" id="Qty_'.$ProductID[$CounterDisplay].'" size=4/> </td>
	<td> <input type="text" class="Sell" name="Sell_'.$ProductID[$CounterDisplay].'" id="Sell_'.$ProductID[$CounterDisplay].'" size=4 value='. $SellPrice[$CounterDisplay] .' /> </td>
	<td> <input type="text" class="Total" name="Total_'.$ProductID[$CounterDisplay].'" id="Total_'.$ProductID[$CounterDisplay].'" size=8 /> </td>
	</tr>';
	$CounterDisplay++;
}

?>
<script>

$('.Quantity, .Sell').keyup(function() {
	subTotal = 0;
	
	$("input[id^='Qty_']").each(function() {
		var str 			= $(this).attr('id');
		var Quantity		= "input#" + str + ".Quantity";									
		var DisplayTotal 	= "input#" + str.replace("Qty","Total") + ".Total";				
		var SellPrice		= "input#" + str.replace("Qty","Sell") + ".Sell";				
		var TotalPrice		= parseFloat( $(Quantity).val() ) * parseFloat( $(SellPrice).val() );	// Calculate total
		
		if (!isNaN(TotalPrice)) {
			$(DisplayTotal).val(TotalPrice.toFixed(2));
		}

	});
	
	$("input[id^='Total_']").each(function() {
		tmpTotal = $(this).val();
		
		if (tmpTotal == "") {
			tmpTotal = 0;
		}
		else {
			subTotal = parseFloat(tmpTotal) + subTotal;
		}
		
		$('input#SubTotal').val(subTotal.toFixed(2));
	});
	
});
	
</script>

</table>

<div id="SubmitInvoice" onclick="gather()">CREATE AN INVOICE</div>

</form>

</div>

</body>
</html>