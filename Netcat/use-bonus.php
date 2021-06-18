<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$_SESSION['nc_netshop_'.$_GET['catalogue'].'_cart']['UsedBonus'] = $_GET['usedBonus']; // Добавляем используемые бонусы в сессию корзины

?>
