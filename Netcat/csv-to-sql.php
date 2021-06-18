<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$key = $_GET['key'];
if(empty($key)) {
	echo "Access denied";
	die;
}
echo date("H:i:s");
global $db;
/*
$url = "vendor_6.csv";

$csv_array = array_map(function($v) {
	$column = str_getcsv($v, ";");
	return array("prodID" => $column[0],"aID" => $column[1]);},file($url)
);

echo date("H:i:s");
global $db;
foreach($csv_array as $data) {
	$sql = "UPDATE `Message1365` SET `ImportSourceID` = '". $data['aID'] ."' WHERE `ProdType` = '6' AND `ItemID` = '". $data['prodID'] ."' LIMIT 1";
	$db->query($sql);
}
*/
/*
$url = "http://stripmag.ru/datafeed/p5s_stock.csv";

$csv_array = array_map(function($v) {
	$column = str_getcsv($v, ";");
	return array("aID" => $column[0],"price" => $column[2],"stock" => $column[4]);},file($url)
);

unset($csv_array[0]);

foreach($csv_array as $row) {
	$sql = "UPDATE `Message1365` SET `Cena_Optovaya`  = '". $row[price] ."', `StockUnits` = '". $row[stock] ."' WHERE `ProdType` = '6' AND `ImportSourceID` = '". $row['aID'] ."' LIMIT 1";
//	$db->query($sql);
	echo $sql ."<br>";
}

/*
echo "<pre>";
print_r(count($csv_array));
echo "</pre>";
*/
echo "Done<br>";
echo date("H:i:s");

?>
