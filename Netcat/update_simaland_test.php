<?

$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -5 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$key = $_GET['key'];
if(!isset($key) || $key != 'Hp5uBftsW') {
	echo "Access denied!";
	die;
}

set_time_limit(0);
ini_set('memory_limit', '10240M');

$nc_core = nc_core::get_object();

$sql = "SELECT ImportSourceID FROM Message1365 WHERE ProdType = '5' LIMIT 100";
$list = (array)$nc_core->db->get_results($sql, ARRAY_A);

echo date("H:i:s") ." Start<br>";


//Получаем token
$auth_url = "https://www.sima-land.ru/api/v5/signin";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $auth_url);
	
$headers = array();
$headers[] = "Content-Type: application/json";
$headers[] = "Accept: application/json";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$post = array("email" => "sales@general-family.ru", "password" => "general-family.ru", "regulation" => true);
$post = json_encode($post);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$auth_output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

$data = explode("\r\n",$auth_output);
$auth_str = explode(": ",$data[1]);

$auth_token = $auth_str[1];

$data_array = array();
/*
function get_sima($id, $token) {
//	$url = "https://www.sima-land.ru/api/v5/item/". $id ."?view=brief";
	$url = "https://www.sima-land.ru/api/v5/item?p=1";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	
	$headers = array();
	$headers[] = "Content-Type: application/json";
	$headers[] = "Accept: application/vnd.simaland.item+json";
	$headers[] = "Authorization: ". $token;
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($output, true);
	$stock = $result['balance'];
	$price = str_replace(',', '.', $result['price']);
	$min_qty = $result['min_qty'];
	$multi = $result['qty_multiplier'];
	$min_order = $result['minimum_order_quantity'];

print_r($result);

	global $data_array;
	
	if($stock) {
		if($stock == 'Достаточно') {
			$stock = '100';
		}
		global $db;
//		$update_sql = "UPDATE Message1365 SET StockUnits = '". $stock ."', Cena_Optovaya = '". $price ."' WHERE ImportSourceID = '". $id ."' LIMIT 1";
		$data_array[] = array("id" => $id, "stock" => $stock, "price" => $price, "min_qty" => $min_qty, "multiplier" => $multi, "min_order" => $min_order);
	}
}

get_sima(1, $auth_token);
*/
/*
$i = 0;
foreach($list as $row) {
	get_sima($row['ImportSourceID'], $auth_token);
	$i++;
	if($i == 400) {
		sleep(1);
		$i = 0;
	}
}
*/
/*
foreach($data_array as $data) {
	$sql = "UPDATE Message1365 SET StockUnits = '". $data['stock'] ."', Cena_Optovaya = '". $data['price'] ."' WHERE ImportSourceID = '". $data['id'] ."' LIMIT 1";
	echo $sql ."<br>";
}
*/
echo date("H:i:s") ."<br>Done";

echo "<pre>";
print_r($auth_output);
echo "<br>";
print_r($data);
echo "</pre>";
?>
