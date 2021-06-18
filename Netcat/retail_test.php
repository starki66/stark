<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

	$order_id = '1928';
	$site = 'general-family.ru';
	$catalogue = 1;
	
	$netshop = nc_netshop::get_instance($catalogue);
	$order = $netshop->load_order($order_id);
	$delivery = $order['DeliveryCost'];
	$items = $order->get_items();
	$order_totals = $items->sum('TotalPrice');
	
	$customer = explode(" ", $order['ContactName']);
	$paymentType = 'cash';
	$paymentStatus = '';
	if($order['PaymentMethod'] == '115') {
		$paymentType = 'bank-card';
		$paymentStatus = 'invoice';
	}
	
$orderItems = array();
foreach($items as $item) {
	if($item['ItemID'] == 0 || $item['ItemID'] == '') {
		$orderItems[] = array('productName' => ''.$item['FullName'].'','initialPrice' => ''.$item['Price'].'','quantity' => ''.$item['Qty'].'','discountManualAmount' => ''.$item['ItemDiscount'].'','offer' => array('externalId' => ''.$item['Class_ID'].'x'.$item['Message_ID'].''));		
	}else{
		$orderItems[] = array('productName' => ''.$item['FullName'].'','initialPrice' => ''.$item['Price'].'','quantity' => ''.$item['Qty'].'','discountManualAmount' => ''.$item['ItemDiscount'].'','offer' => array('id' => ''.$item['ItemID'].''));		
	}


//		$orderItems[] = array('productName' => ''.$item['FullName'].'','initialPrice' => ''.$item['Price'].'','quantity' => ''.$item['Qty'].'','discountManualAmount' => ''.$item['ItemDiscount'].'','offer' => array('id' => ''.$item['ItemID'].'','externalId' => '1398x346'));
//		$orderItems[] = array('productName' => ''.$item['FullName'].'','initialPrice' => ''.$item['Price'].'','quantity' => ''.$item['Qty'].'','discountManualAmount' => ''.$item['ItemDiscount'].'','offer' => array('id' => '906'));
}

$url = 'https://skygarant.retailcrm.ru/api/v5/orders/create';
$crmKey = 'PZx6ZZShGT78sfBEy1taa8tIBHygP0XS';

$postData = http_build_query(array(
	'site' => ''.$site.'',
    'order' => json_encode(array(
        'firstName' => 'Тест',
        'lastName' => 'Тестовый',
        'patronymic' => ''.$customer[2].'',
		'externalId' => ''.$order_id.'',
        'phone' => ''.$order['Phone'].'',
        'email' => ''.$order['Email'].'',
		'customFields' => array(
			'netcat' => ''.$order_id.''
		),
		'delivery' => array(
			'address' => array(
				'city' => ''.$order['City'].'',
				'text' => ''.$order['Address'].''
			)
		),
		'payments' => array(
			array(
				'type' => ''. $paymentType .'',
				'status' => ''. $paymentStatus .''
			)
		),
        'items' => $orderItems
)),
    'apiKey' => $crmKey,
));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_FAILONERROR, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

$output = curl_exec($ch);
curl_close($ch);

$result = json_decode($output, true);
print_r($result);
$extarnalOrderID = $result['id'];
?><br /><br />
<?
echo $result['order']['payments'][0]['id'];
?>
