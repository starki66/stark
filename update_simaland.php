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

	$flog = fopen('simaland.log', 'a') or die("File Sima does not open");
	$start_log = "". date("Y-m-d H:i:s") ." Start Script\n";
	fwrite($flog, $start_log);
	fclose($flog);

$nc_core = nc_core::get_object();

$sql = "SELECT ImportSourceID FROM Message1365 WHERE ProdType = '5'";
$list = (array)$nc_core->db->get_results($sql, ARRAY_A);

	$flog = fopen('simaland.log', 'a') or die("File Sima does not open");
	$count_log = "". date("Y-m-d H:i:s") ." Count Messages ". count($list) ."\n";
	fwrite($flog, $count_log);
	fclose($flog);

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
curl_close($ch);

$data = explode("\r\n",$auth_output);
$auth_str = explode(": ",$data[4]);

global $auth_token;
$auth_token = $auth_str[1];

	$flog = fopen('simaland.log', 'a') or die("File Sima does not open");
	$token_log = "". date("Y-m-d H:i:s") ." Token | ". $auth_token ."\n";
	fwrite($flog, $token_log);
	fclose($flog);

function get_sima($id, $auth_token) {
//	$url = "https://www.sima-land.ru/api/v5/item/". $id ."?view=brief";
	$url = "https://www.sima-land.ru/api/v5/item/". $id ."";
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	
	$headers = array();
	$headers[] = "Content-Type: application/json";
	$headers[] = "Accept: application/vnd.simaland.item+json";
	$headers[] = "Authorization: ". $auth_token;
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($output, true);
	
	$multi = $result['qty_multiplier'];
	$price = str_replace(',', '.', $result['price']);
	$stock = $result['balance'];

	if($stock) {
		if($stock == 'Достаточно' || $stock > 1) {
			$stock = '10000';
		}
		global $db;
		$update_sql = "UPDATE Message1365 SET StockUnits = '". $stock ."', Cena_Optovaya = '". $price ."', MinOrderQty = '". $multi ."' WHERE ImportSourceID = '". $id ."' LIMIT 1";
		$db->query($update_sql);
//		echo $update_sql."<br>";

	$flog = fopen('simaland.log', 'a') or die("File Sima does not open");
	$time_log = "". date("Y-m-d H:i:s") ." ". $id ." - ". $stock .", ". $price .",". $multi ."\n";
	fwrite($flog, $time_log);
	fclose($flog);
	
	}
}

//Если получен token запускаем функцию обновления
if($auth_token) {
	$i = 0;
	foreach($list as $row) {
		get_sima($row['ImportSourceID'], $auth_token);
		$i++;
		if($i == 400) {
			sleep(1);
			$i = 0;
		}
	}
}

	$flog = fopen('simaland.log', 'a') or die("File Sima does not open");
	$finish_log = "". date("Y-m-d H:i:s") ." Finish\n";
	fwrite($flog, $finish_log);
	fclose($flog);

echo date("H:i:s") ."<br>Done";
?>
<?// print_r($output) ?>
