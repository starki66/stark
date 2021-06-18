<?

$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$netshop = nc_netshop::get_instance();
$goods_components = $netshop->get_goods_components_ids();
global $db;

$sub_from = $_GET['sub_from'];
$sub_to = $_GET['sub_to'];
$count = $_GET['count'];

if($sub_from){
	$sql = "SELECT Class_ID FROM Sub_Class WHERE Subdivision_ID = ". $sub_from ."";
	$cc_array = $db->get_results($sql, ARRAY_A);

	$cc_ids = array();
	foreach($cc_array as $item) {
		$cc_ids[] = $item['Class_ID'];
	}
	$goods_component = array_intersect($cc_ids, $goods_components);

	global $class_from;
	$class_from = $goods_component[0];

	$sql_count = "SELECT count(Message_ID) FROM Message". $class_from ." WHERE Subdivision_ID = ". $sub_from ."";
	$count_goods = $db->get_var($sql_count);

	$out_data = [];
	
	if($count == 1 && $count_goods) {
		$out_data[] = "Выбрано товаров: ". $count_goods ."";
		$out_data[] = $class_from;
		echo json_encode($out_data);
	}
	if(!$count_goods) {
		$out_data[] = "Нет товаров для переноса";
		echo json_encode($out_data);
	}
}

if($sub_to){
	$sql = "SELECT Class_ID FROM Sub_Class WHERE Subdivision_ID = ". $sub_to ."";
	$cc_array = $db->get_results($sql, ARRAY_A);
	
	$cc_ids = array();
	foreach($cc_array as $item) {
		$cc_ids[] = $item['Class_ID'];
	}
	$goods_component = array_intersect($cc_ids, $goods_components);

	global $class_to;
	$class_to = $goods_component[0];

	$sql_to = "SELECT Sub_CLass_ID FROM Sub_Class WHERE Subdivision_ID = ". $sub_to ." AND Class_ID = ". $class_to ."";
	$sub_class_to = $db->get_var($sql_to);
	
	$out_data = [];
	
	if($class_to != $class_from) {
		$out_data[] = "<span style='color:red'>Принимающий компонент не соответствует исходному</span>";
		echo json_encode($out_data);
	}else{
		$out_data[] = "Подготовка данных для переноса завершена";
		$out_data[] = $sub_class_to;
		$out_data[] = "1";
		echo json_encode($out_data);
	}
}

?>
