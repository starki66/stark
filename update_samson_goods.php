<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$key = $_GET['key'];
if(!isset($key) || $key != 'Hp5uBftsW') {
	echo "Access denied!";
	die;
}


/*
global $db;
$markup = $nc_core->db->get_var("SELECT `Samson` from Message3513 LIMIT 1");
*/

$url = 'https://api.samsonopt.ru/v1/sku/?api_key=75401207fa2896ab40024c8e9e0677f3&pagination_count=250';

echo date("H:i:s");

// Получим товары
function get_samson_goods($url) {
//	$curl = curl_init('https://api.samsonopt.ru/v1/sku/?api_key=75401207fa2896ab40024c8e9e0677f3&pagination_count=250&pagination_page=' . $next_page . '');
	$curl = curl_init($url);
	$arHeaderList = array();
	$arHeaderList[] = 'Accept: application/json';
	$arHeaderList[] = 'User-Agent: string';
	curl_setopt($curl, CURLOPT_HTTPHEADER, $arHeaderList);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	$output = curl_exec($curl);
	curl_close($curl);

	$result = json_decode($output, true);
	global $db, $url, $i;
	$url = $result['meta']['pagination']['next'];

	foreach($result['data'] as $data) {
		$price = str_replace(',', '.', $data['price_list']['0']['value']);
		$sql = "UPDATE `Message1365` SET `Cena_Optovaya` = '". $price ."', `StockUnits` = '". $data['stock_list']['0']['value'] ."' WHERE `ProdType` = '3' AND ImportSourceID = '". $data['sku'] ."' LIMIT 1";
		$db->query($sql);
	}
$i++;
	if($url) {
		get_samson_goods($url);
	}

}
if($url) {
	get_samson_goods($url);
}

echo "<br>Done - Samson updated<br>";
echo date("H:i:s");
?>