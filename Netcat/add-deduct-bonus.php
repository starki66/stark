<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

/*
Добавление/вычитание бонусов у юзера после перехода
заказа в статус Выполнен в RetailCRM
а также изменение статуса заказа в админке на Завершен
*/

$retail_id = $_GET['order'];
$order_id = $nc_core->db->get_var("SELECT Message_ID FROM Message34 WHERE ExternalID = ". $retail_id ."");

$bonus_add = $_GET['bonus'];
$netshop = nc_netshop::get_instance($catalogue);
$order = $netshop->load_order($order_id);
$bonus_deduct = $order['UsedBonus'];
$user_id = $order['User_ID'];

// меняем статус заказа на Завершен
$nc_core->db->query("UPDATE Message34 SET Status = 5 WHERE Message_ID = ". $order_id ."");

// Если есть оплата бонусами, списываем их
if($bonus_deduct > 0) {
	$nc_auth->pa_deduct($bonus_deduct, $user_id, 'Оплата заказа № '.$order_id.'');
}

// Если покупатель зарегистрирован, добавляем ему бонусы
if($user_id > 0) {
	$nc_auth->pa_add($bonus_add, $user_id, 'Бонусы за заказ № '.$order_id.'');
}


?>
