<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$key = $_GET['key'];
if(!isset($key) || $key != 'Hp5uBftsW') {
	echo "Access denied!";
	die;
}


$nc_core = nc_core::get_object();
//$markup = $nc_core->db->get_var("SELECT `Komus` from Message3513 LIMIT 1");

$next_page = '1';
echo date("H:i:s");
function get_komus_price($next_page) {
	$url = 'http://www.komus-opt.ru/api/elements/?token=605464d5975089955da9282b8c3322fcd7aabe569d899363e929583f9b14bc0b21ea61cc6c5672fa1de7ccebdd8e8e6878082d809e418882&format=json&count=250&page=' . $next_page . '';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($curl);
	curl_close($curl);

	global $next_page;
	global $db;
//	global $markup;
	
	$result = json_decode($output, true);
	
	foreach($result['content'] as $row) {
//Запрос с наценкой
//		$sql = "UPDATE `Message1365` SET `Cena_Optovaya` = '". $row['price'] ."', `StockUnits` = '". $row['remains'] ."', `Price` = round(". $row['price'] ."*". $markup .") WHERE `ProdType` = '4' AND `ImportSourceID` = '". $row['id'] ."' LIMIT 1";
//Запрос без наценки
		$sql = "UPDATE `Message1365` SET `Cena_Optovaya` = '". $row['price'] ."', `StockUnits` = '". $row['remains'] ."' WHERE `ProdType` = '4' AND `ImportSourceID` = '". $row['id'] ."' LIMIT 1";		
		$db->query($sql);
	}

	$next_page = $result['next'];

	if($next_page) {
		get_komus_price($next_page);
	}
}

if($next_page) {
	get_komus_price($next_page);
}


echo "<br>Komus updated<br/>";
echo date("H:i:s");

?>
