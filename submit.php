<?

$Data = $_POST['input'];

print_r($Data);

$Date 		= $Data[0];
$FirstName 	= $Data[1];
$LastName	= $Data[2];
$Email		= $Data[3];

// $stmt = $db->prepare('INSERT INTO Customers(FirstName, LastName, Email) VALUES(?, ?, ?)');
// $stmt->bindParam(1, $FirstName);
// $stmt->bindParam(2, $LastName);
// $stmt->bindParam(3, $Email);
// $stmt->execute();
// catch(PDOException $e) { echo $e->getMessage(); } */

	// 1. Get InvoiceID + 1
	// 2. Loop through ProductID, Qty and SellPrice
	// 3. Customer ID already selected above
	// 4. Submit SQL query. Return invoice number.
	
?>

