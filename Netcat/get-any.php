<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$key = $_GET['key'];
echo "Done";
/*
if(!isset($key)) {
	echo "Доступ запрещен";
	die;
}

$nc_core = nc_core::get_object();
$netshop = nc_netshop::get_instance();
$goods_component_ids = $netshop->get_goods_components_ids();


if($key == 'choose-city') {
	$city = $_GET['city'];
	$user_id = $_GET['user_id'];
	setcookie('Order_City', $city, time() + 31536000, '/');
	if($user_id) {
		$sql = "UPDATE User SET City = '$city' WHERE User_ID = '$user_id'";
		$nc_core->db->query($sql);
	}
}
/*
//Получение поля (vendor) из xml файла
$file = '3452';
$xml = simplexml_load_file($file.'.xml');
$vendor_array = array();
foreach ($xml->shop->offers->offer as $offer) {
   $vendor_array[] = $offer->vendor;
}

$vendor = array_unique($vendor_array);

$delimiter = ';';
$fp = fopen('output/'.$file.'.csv', 'w');
fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM
foreach ($vendor as $v) {
	$arr_fld = array($v[0]);
    fputcsv($fp, $arr_fld, $delimiter);
}
fclose($fp);

echo "<pre>";
print_r($vendor);
echo "</pre>";

*/

/*
//Добавление записи ко всем товарам
if($key = 'txt') {
	foreach ($goods_component_ids as $id) {
		$nc_core->db->query("UPDATE Message$id SET SalesNotes = 'Скидка 3% при оплате на сайте!'");
	}
echo count($goods_component_ids);
}
*/

/*
//снижение цены на 20%
if($key = 'price') {
	foreach ($goods_component_ids as $id) {
		if($id != '1365') {
			$nc_core->db->query("UPDATE Message$id SET Price = round((Price / 120)*100)");
		}
	}
echo count($goods_component_ids);
}
*/


/*
//Удаление "в ассортименте" в названии товара
$count = '1';
foreach ($goods_component_ids as $id) {
	$cnt = $nc_core->db->get_var("select count(Message_ID) from Message$id where Name like '% в ассортименте'");
	$nc_core->db->query("update Message$id set Name = REPLACE(Name, ' ассортимент', '') where Name like '% ассортимент'");
	if($cnt != '') {
		$count += $cnt;
	}
}

echo $count;
*/

/*
//Удаление Старой цены (OldPrice) в товаре
$count = '1';
foreach ($goods_component_ids as $id) {
	$cnt = $nc_core->db->get_var("select count(Message_ID) from Message$id where OldPrice != ''");
	$nc_core->db->query("update Message$id set OldPrice = '' where OldPrice != ''");
	if($cnt != '') {
		$count += $cnt;
	}
}

echo $count;
*/

/*
//Выставление остатков на складе по производителю
$count = '1';
foreach ($goods_component_ids as $id) {
	$cnt = $nc_core->db->get_var("select count(Message_ID) from Message$id where Vendor = 'Gipfel'");
	$nc_core->db->query("update Message$id set StockUnits = 0 where Vendor = 'Gipfel'");
	if($cnt != '') {
		$count += $cnt;
	}
}

echo $count;
*/
?>
