<?
$NETCAT_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -4 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($INCLUDE_FOLDER."index.php");

$nc_core = nc_core::get_object();
$domain = 'https://general-family.ru';

$subs_array = nc_get_sub_children(15, true);

$sitemap = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

$sitemap .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

foreach($subs_array as $sub) {
	$sub_data = $nc_core->db->get_results("SELECT Hidden_URL, DATE_FORMAT(LastUpdated, '%Y-%m-%d') AS LastUpdated FROM Subdivision WHERE Subdivision_ID = '". $sub ."'", ARRAY_A);
	$sitemap .= "<url>";
	$sitemap .= "<loc>". $domain ."". $sub_data[0]['Hidden_URL'] ."</loc>";
	$sitemap .= "<lastmod>". $sub_data[0]['LastUpdated'] ."</lastmod>";
	$sitemap .= "</url>\n";
}

$sitemap .= "</urlset>";


$fs = fopen("/var/www/skygarant_popov/data/www/general-family.ru/sitemapsubs.xml", "w") or die("File Sitemap does not open");
fwrite($fs, $sitemap);
fclose($fs);

/*
echo "<pre>";
print_r(nc_get_sub_children(15, true));
echo "</pre>";
*/
?>
