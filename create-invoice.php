<?

// --------------------TO-DO LIST--------------------
// x. Auto-calculate total cost of stock when Qty is changed
// x. Auto-calculate sub-totals/GST/Total - * revist with Phill *
// x. Ensure today's date appears by default
// 4. Submit an invoice successfully to SQL
// 4a. <Error check> if NO first or surname is present OR if nothing is purchased
// 5. Submit a draft successfully to SQL (un-paid)
// 6. Create animated confirmation of invoice being submitted successfully
// 7. Create printable invoice (seperate link) which fits A4 page
// --------------------------------------------------
// 
// Searchbox/Dropdown to select Exisiting Customers
// Limit QTY field to 1 numerical digit.
// Check if QTY field is blank, if so: clear TOTAL field
// Restrict numerical fields to only be able to use digits
//
// --------------------------------------------------

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

<link rel="stylesheet" type="text/css" href="css/style.css">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.10.2.custom.css">
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/jquery.watermark.min.js"></script>

<script>

$(function() {
	$("#DateDisplay").datepicker({ 
	showOn: "button",
	buttonImage: "img/calendar.png",
	buttonImageOnly: true,
	autoSize: true,
	dateFormat: 'dd/mm/yy',
	});
	
	$("#FirstName").watermark("First Name");
	$("#LastName").watermark("Surname");
	$("#Email").watermark("E-mail Address");
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

<body>
<form method="post" action="" id="MainForm">

<div id="Main">
<span id="Title">Tax Invoice</span>

<input type="text" id="DateDisplay" name="Date" value="<? echo $TodayDate; ?>"> <p>

<input type="text" name="FirstName" id="FirstName"> 
<input type="text" name="LastName" id="LastName"> <br>
<input type="text" name="Email" id="Email"> <p>

<table border=0 id="Stock">

<td class="HeaderDisplayName"></td>
<td class="HeaderQty">Qty</td>
<td class="HeaderSell">Sell</td>
<td class="HeaderTotal">Total</td>

<? 

// Display data fetched from database: Stock Name, Stock ID and Sell Price
foreach($ProductID as $value) {
	echo '
	<tr>
	<td class="DisplayName"> <b>'. $DisplayName[$CounterDisplay] .'</b> </td>
	<td> <input type="text" class="Quantity" name="Qty_'.$ProductID[$CounterDisplay].'" id="Qty_'.$ProductID[$CounterDisplay].'" size=1 /> </td>
	<td> <input type="text" class="Sell" name="Sell_'.$ProductID[$CounterDisplay].'" id="Sell_'.$ProductID[$CounterDisplay].'" size=3 value='. $SellPrice[$CounterDisplay] .' /> </td>
	<td> <input type="text" class="Total" name="Total_'.$ProductID[$CounterDisplay].'" id="Total_'.$ProductID[$CounterDisplay].'" size=3 /> </td>
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
<p>

<table border=0 id="PriceTotals">

<tr>
<td> Sub Total: </td>
<td> <input type="text" id="SubTotal" name="SubTotal" size=3 /> </td>
</tr>
<tr>
<td> GST: </td> 
<td> <input type="text" id="GST" name="GST"  size=3 /> </td>
</tr>
<tr>
<td>Total: </td>
<td> <input type="text" id="Total" name="Total" size=3 /> </td>
</tr>
<tr>
<td>Paid? </td>
<td> <input type="checkbox" id="Paid" name="Paid"> </td>
</tr>

</table>

<!-- <input type="submit" name="submit" value="Submit Invoice" /> -->

<input id="ClickMe" type="button" value="Gather!" onclick="gather()" />

</form>

</div>

</body>
</html>