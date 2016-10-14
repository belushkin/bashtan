<?php

## stara opcja
## $tnij = explode("wp-content",$_SERVER["PHP_SELF"]);
## require_once($_SERVER['DOCUMENT_ROOT'].$tnij[0].'wp-load.php');

require_once('../../../wp-load.php');

/*
$sql = mysql_query("SELECT `option_name`,`option_value` FROM `wp_options` WHERE `option_name` LIKE 'si_order'");

while ($wiersz = mysql_fetch_assoc($sql)) {

		$orderby = $wiersz['option_value'];

	}

$sql = mysql_query("SELECT `option_name`,`option_value` FROM `wp_options` WHERE `option_name` LIKE 'si_hidden'");

while ($wiersz = mysql_fetch_assoc($sql)) {

		$showhidden = $wiersz['option_value'];

	}

$sql = mysql_query("SELECT `option_name`,`option_value` FROM `wp_options` WHERE `option_name` LIKE 'si_pages'");

while ($wiersz = mysql_fetch_assoc($sql)) {

		$showpages = $wiersz['option_value'];

	}

echo $orderby.$showhidden.$showpages;*/



		//Get option values
		$orderby = get_option('si_order'); #echo $orderby;
		$showhidden = get_option('si_hidden'); #echo $showhidden;
		$showpages = get_option('si_pages'); #echo $showpages;
		$showtags = get_option('si_tags');
		$showcateg = get_option('si_categ');
		$manylinks = get_option('si_links');

		//do the order by
		switch ($orderby) {
			case 'date_descending':
				$sqlorder = "ORDER BY post_date DESC";
				break;
			case 'date_ascending':
				$sqlorder = "ORDER BY post_date";
				break;
			case 'alpha_descending':
				$sqlorder = "ORDER BY post_title";
				break;
			case 'alpha_ascending':
				$sqlorder = "ORDER BY post_title DESC";
				break;
		}
		
		//show private
		if ($showhidden == 'on') {
			$sqlwhere = "WHERE post_type='post' ";
		} else {
			$sqlwhere = "WHERE post_type='post' AND post_status='publish' ";
		}



$co = $_GET['c'];
$od = $_GET['s'];
$po_ile_postow = $manylinks;

$siteurl = get_option('siteurl');


$xmlForHeader = '<?xml version="1.0" encoding="UTF-8"?>
<!-- Created by Sitemap Index plug-in for Wordpress version 1.2.3 (http://wordpress.org/extend/plugins/sitemap-index/) -->
';


if (isset($od)&&!isset($co)) {

$xmlForHeader .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$data=date("Y-m-d");

$xmNOone = "	<url>
		<loc>".$siteurl."</loc>
		<lastmod>$data</lastmod>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>
";
	
$xmForTresc .= $xmNOone;



	//Do we want the pages too?
	if ($showpages == 'on') {
		$sqlpages = "SELECT * FROM " . $table_prefix . "posts where post_type='page' ";

		if ($showhidden != 'on') {
			$sqlpages .= "AND post_status='publish' ";
		}
		
		$allpages = $wpdb->get_results($sqlpages);

foreach($allpages as $ap) {
$perma = get_permalink($ap->ID);
$data = $ap->post_date;
$czesc = explode(" ",$data);
$data = $czesc[0];

if ($data==date("Y-m-d")) {$priority='1';$changefreq='daily';} // z dzisiaj
else if ($data>=(date("Y-m-d", time() - (60*60*24*2)))) {$priority='0.9';$changefreq='daily';} //od dzi¶ do 2 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*4)))) {$priority='0.8';$changefreq='daily';} //od 2 do 4 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*7)))) {$priority='0.7';$changefreq='weekly';} //od 4 do 7 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*14)))) {$priority='0.6';$changefreq='weekly';} //od 7 do 14 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*21)))) {$priority='0.5';$changefreq='weekly';} //od 14 do 21 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*30)))) {$priority='0.4';$changefreq='monthly';} //od 21 do 30 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*90)))) {$priority='0.3';$changefreq='monthly';} //od 30 do 90 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*180)))) {$priority='0.2';$changefreq='monthly';} //od 90 do 180 dni temu
else {$priority='0.1';$changefreq='yearly';} // starcze ni¿ 180 dni

$xmlForOffer ="	<url>
		<loc>$perma</loc>
		<lastmod>$data</lastmod>
		<changefreq>$changefreq</changefreq>
		<priority>$priority</priority>
	</url>
";

$xmForTresc .= $xmlForOffer;

			}

		}



$sql = "SELECT * FROM " . $table_prefix . "posts " . $sqlwhere . $sqlorder." LIMIT ".$od.",".$po_ile_postow;

$allposts = $wpdb->get_results($sql);

foreach($allposts as $ap) {
	$perma = get_permalink($ap->ID);
	$data = $ap->post_date;
	$czesc = explode(" ",$data);
	$data = $czesc[0];


if ($data==date("Y-m-d")) {$priority='1';$changefreq='daily';} // z dzisiaj
else if ($data>=(date("Y-m-d", time() - (60*60*24*2)))) {$priority='0.9';$changefreq='daily';} //od dzi¶ do 2 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*4)))) {$priority='0.8';$changefreq='daily';} //od 2 do 4 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*7)))) {$priority='0.7';$changefreq='weekly';} //od 4 do 7 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*14)))) {$priority='0.6';$changefreq='weekly';} //od 7 do 14 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*21)))) {$priority='0.5';$changefreq='weekly';} //od 14 do 21 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*30)))) {$priority='0.4';$changefreq='monthly';} //od 21 do 30 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*90)))) {$priority='0.3';$changefreq='monthly';} //od 30 do 90 dni temu
else if ($data>=(date("Y-m-d", time() - (60*60*24*180)))) {$priority='0.2';$changefreq='monthly';} //od 90 do 180 dni temu
else {$priority='0.1';$changefreq='yearly';} // starcze ni¿ 180 dni

$xmlForOffer ="	<url>
		<loc>$perma</loc>
		<lastmod>$data</lastmod>
		<changefreq>$changefreq</changefreq>
		<priority>$priority</priority>
	</url>
";



$xmForTresc .= $xmlForOffer;

}

$xmlForFooter ='</urlset>';

}

else if (($co == 'tg')&&($showtags == 'on')) {

$xmlForHeader .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$data=date("Y-m-d");	

$xmNOone = "	<url>
		<loc>".$siteurl."</loc>
		<lastmod>$data</lastmod>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>
";
	
$xmForTresc .= $xmNOone;

$tags = get_terms("post_tag",array("hide_empty"=>true,"hierarchical"=>false));
if($tags && is_array($tags) && count($tags)>0) {
foreach($tags AS $tag) {
$perma = get_tag_link($tag->term_id);

$xmlForOffer ="	<url>
		<loc>$perma</loc>
		<lastmod>$data</lastmod>
		<changefreq>weekly</changefreq>
		<priority>0.4</priority>
	</url>
";

$xmForTresc .= $xmlForOffer;
}

$xmlForFooter ='</urlset>';

}

}

else if (($co == 'ct')&&($showcateg == 'on')) {

$xmlForHeader .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$data=date("Y-m-d");

$xmNOone = "	<url>
		<loc>".$siteurl."</loc>
		<lastmod>$data</lastmod>
		<changefreq>daily</changefreq>
		<priority>1</priority>
	</url>
";
	
$xmForTresc .= $xmNOone;

$cats = get_terms("category",array("hide_empty"=>true,"hierarchical"=>false));
if($cats && is_array($cats) && count($cats)>0) {

foreach($cats AS $cat) {

$perma = get_category_link($cat->term_id);

$xmlForOffer ="	<url>
		<loc>$perma</loc>
		<lastmod>$data</lastmod>
		<changefreq>weekly</changefreq>
		<priority>0.5</priority>
	</url>
";

$xmForTresc .= $xmlForOffer;
}

$xmlForFooter ='</urlset>';

}

}

else

{

$xmlForHeader .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';

$data=date("Y-m-d");

$sql_2 = "SELECT `ID`, Count(*) as 'numm' FROM ".$table_prefix."posts ".$sqlwhere. $sqlorder."";
$res_2 = $wpdb->get_results($sql_2);
foreach($res_2 as $ap) {
$ile_postow = $ap->numm;
}

#echo $table_prefix." \n";
#echo $sqlwhere." \n";
#echo $sqlorder." \n";
#echo $sql_2." \n";
#echo $ile_postow." tyle ich jest \n\n";

if ($ile_postow<=0) {$ile_postow=1;}
if ($po_ile_postow<=0) {$po_ile_postow=1;}

$ile_map_postow = ceil($ile_postow / $po_ile_postow);
for($i=0;$i<$ile_map_postow;$i++) {

$od = $i*$po_ile_postow;


$xmlForOffer ="	<sitemap>
		<loc>".$siteurl."/wp-content/plugins/sitemap-index/gen_sitemap.php?s=$od</loc>
		<lastmod>$data</lastmod>
	</sitemap>
";

$xmForTresc .= $xmlForOffer;
}


if ($showtags == 'on') {
$xmlForOffer ="	<sitemap>
		<loc>".$siteurl."/wp-content/plugins/sitemap-index/gen_sitemap.php?c=tg</loc>
		<lastmod>$data</lastmod>
	</sitemap>
";

$xmForTresc .= $xmlForOffer;}


if ($showcateg == 'on') {
$xmlForOffer ="	<sitemap>
		<loc>".$siteurl."/wp-content/plugins/sitemap-index/gen_sitemap.php?c=ct</loc>
		<lastmod>$data</lastmod>
	</sitemap>
";

$xmForTresc .= $xmlForOffer;}



$xmlForFooter ='</sitemapindex>';
		
}


header('Content-Type: application/xml');
echo ($xmlForHeader.$xmForTresc.$xmlForFooter);


?>