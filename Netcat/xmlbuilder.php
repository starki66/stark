<?php
$startTime = microtime(true);
$tmpFile = __DIR__ . "/output/all-goods-tmp.xml";
$dstFile = __DIR__ . "/output/all-goods.xml";

define('DOMAIN', 'https://general-family.ru');

echo "{$tmpFile}\r\n";
if (file_exists($tmpFile)) {
    unlink($tmpFile);
}

$mysqli = new mysqli("localhost", "general_family", "HzU-Wm3Gtr=sDp", "general_family");
if (!$mysqli->set_charset("utf8")) {
    printf("Error: %s\n", $mysqli->error);
    exit();
}

function appendOutput($filename, $data)
{
    $fh = fopen($filename, "a+");
    fwrite($fh, $data);
    fclose($fh);
}

function fetchQuery(&$db, $query, $index = null)
{
    $result = [];
    $list = $db->query($query);
    while ($row = $list->fetch_assoc()) {
        if (is_null($index)) {
            $result[] = $row;
        } else {
            $result[$row[$index]] = $row;
        }
    }
    return $result;
}

function fetchQueryMap(&$db, $query, $index, $valueKey)
{
    $result = [];
    $list = $db->query($query);
    while ($row = $list->fetch_assoc()) {
        $result[$row[$index]] = $row[$valueKey];
    }
    return $result;
}

function buildCatalogUrl(&$base, $idx, $url = '')
{
    if (!isset($base[$idx])) {
        return null;
    }
    if ($url == '') {
        $url = '/' . $base[$idx]['EnglishName'];
    }
    $url = "/{$base[$idx]['EnglishName']}{$url}";
    if ($base[$idx]['Parent_Sub_ID'] != 0) {
        return buildCatalogUrl($base, $base[$idx]['Parent_Sub_ID'], $url);
    }
    return $url;
}

function xml_title($title)
{
    $m = ["&amp;", "&lt;", "&gt;", "&quot;", "&apos;"];
    $c = ["&", "<", ">", "\"", "'"];
    $r = ["«", "»", "#", "?", "@", '{', '}'];
    $title = str_replace($m, $c, $title);
    $title = str_replace($r, '', $title);
    return trim(str_replace($c, $m, $title));
}

appendOutput($tmpFile, '<?xml version="1.0" encoding="utf-8"?>');
appendOutput($tmpFile, '<yml_catalog><shop><name>General-family</name><company>General-family</company><categories>');
///// Categories
$allSubdiv = "SELECT Subdivision_ID, Parent_Sub_ID, Subdivision_Name, EnglishName FROM Subdivision";
$allSubdiv = fetchQuery($mysqli, $allSubdiv, 'Subdivision_ID');

$productsSubdivisions = "SELECT 
	Subdivision_ID
FROM Subdivision WHERE Subdivision_ID IN (
	SELECT DISTINCT Subdivision_ID FROM Message1365 WHERE Checked = 1 AND Parent_Message_ID = 0
)";
$productsSubdivisions = fetchQuery($mysqli, $productsSubdivisions, 'Subdivision_ID');
foreach ($productsSubdivisions as $sdId => $sdArr) {
    if (!isset($allSubdiv[$sdId])) {
        unset($productsSubdivisions[$sdId]);
        continue;
    }
    $productsSubdivisions[$sdId] = buildCatalogUrl($allSubdiv, $sdId);
    if (is_null($productsSubdivisions[$sdId])) {
        unset($productsSubdivisions[$sdId]);
        continue;
    }
    $title = xml_title($allSubdiv[$sdId]['Subdivision_Name']);
    appendOutput($tmpFile, "<category id=\"{$sdId}\">{$title}</category>");
}
unset($allSubdiv);
appendOutput($tmpFile, '</categories></shop><offers>');
///// Vendors
$vendors = "SELECT Message_ID, `Name` FROM Message3505 WHERE Checked = 1";
$vendors = fetchQueryMap($mysqli, $vendors, 'Message_ID', 'Name');
///// Products
$nextPage = true;
$startPoint = 0;
$pageLimit = 20000;
while ($nextPage) {
    $products = "SELECT 
       m.`Message_ID` AS `Message_ID`,
       m.`Name` AS `Name`,
       m.`Article` AS `Article`,
       m.`Price` AS `Price`,
       m.`Image` AS `Image`,
       m.`BarCode` AS `BarCode`,
       m.`VendorCode` AS `VendorCode`,
        m.`Cena_Optovaya` AS PurchasePrice,
        m.`Weight` AS Weight,
        m.`ProdType` AS Vendor,
        m.`StockUnits` AS StockUnits,
       m.`Subdivision_ID` AS `Subdivision_ID`
       FROM `Message1365` AS m
       WHERE m.`Parent_Message_ID` = 0
       ORDER BY m.`Message_ID` DESC
    LIMIT {$startPoint}, {$pageLimit}";
    $products = fetchQuery($mysqli, $products);
    if (empty($products)) {
        break;
    }
    foreach ($products as $product) {
        if (!isset($productsSubdivisions[$product['Subdivision_ID']])) {
            continue;
        }
        if (!isset($vendors[$product['Vendor']])) {
            continue;
        }
        $title = xml_title($product['Name']);
        $vendor = xml_title($vendors[$product['Vendor']]);
        if ($title == '') {
            continue;
        }
        $product['Price'] = str_replace(',', '.', $product['Price']);
        $product['PurchasePrice'] = str_replace(',', '.', $product['PurchasePrice']);
        $product['Weight'] = str_replace(',', '.', $product['Weight']) / 1000;
        if ($product['Weight'] < 0.001) {
            $product['Weight'] = 0;
        }
        $product['Weight'] = str_replace(',', '.', (string)$product['Weight']);
        $url = DOMAIN . $productsSubdivisions[$product['Subdivision_ID']] . "_{$product['Message_ID']}.html";
        $img = explode(':', $product['Image']);
        $img = trim($img[count($img) - 1]);
        if ($img != '') {
            $img = DOMAIN . "/netcat_files/{$img}";
        }
        $offer = '';
        $offer .= "\n<offer id=\"{$product['Article']}\" productID=\"{$product['Article']}\" quantity=\"10000\">";
        $offer .= "<name>{$title}</name>";
        $offer .= "<productName>{$title}</productName>";
        $offer .= "<categoryId>{$product['Subdivision_ID']}</categoryId>";
        $offer .= "<price>{$product['Price']}</price>";
        $offer .= "<purchasePrice>{$product['PurchasePrice']}</purchasePrice>";
        if ($img) {
            $offer .= "<picture>{$img}</picture>";
        }
        $offer .= "<url>{$url}</url>";
        $offer .= "<vendor>{$vendor}</vendor>";
        $offer .= "<barcode>{$product['BarCode']}</barcode>";
        $offer .= "<weight>{$product['Weight']}</weight>";
        $offer .= "<param name=\"Артикул\" code=\"article\">{$product['Article']}</param>";
        $offer .= "<param name=\"Артикул поставщика\" code=\"vendorcode\">{$product['VendorCode']}</param>";
        $offer .= "</offer>";
        appendOutput($tmpFile, $offer);
    }
    $startPoint += $pageLimit;
    echo "+";
}
///// FINISH
appendOutput($tmpFile, '</offers></yml_catalog>');
exec("rm -rf {$dstFile} && mv {$tmpFile} {$dstFile} && rm -rf {$tmpFile}");
echo "\r\n--------" . number_format(microtime(true) - $startTime, 6, '.', ' ') . "--------\r\n";
