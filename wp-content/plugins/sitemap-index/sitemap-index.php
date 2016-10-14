<?php
/*
Plugin Name: Sitemap Index
Plugin URL: http://twardes0.webd.pl/wordpress/sitemap-index-plugin
Description: Automaticly generates virtual sitemaps and sitemap index in XML format. One-Click Sitemaps.
Author: Twardes
Version: 1.2.3
Author URI: http://www.atmail.pl/
*/

/*
== Changelog ==

1.2.3

*	Compatibility checked.
*	Plugin home site changed.
*	Removal of inactive submission methods.

1.2.2

*	Plugin home site changed.
*	Compatibility checked.

1.2.1

*	Fixed sitemap adding. Added second sitemap ping method
*	Added manual method sitemap submitting.

1.2

*	Changed sitemap submission function. Now submits to all search engines at once.
*	Improved sitemap generation.

1.1

*	Added tags indexing functionality
*	Added categories indexing functionality
*	Changes in administrator panel
*	PHP code fixes

1.0.3

*	File paths changed to more universal
*	Added new sitemap submission links
*	Added settings link in plugins menu

1.0.2

*	Fixed sitemap link.
*	Fixed path to blog main directory.

1.0.1

*	Added new links in administrator panel

1.0a

*	Alfa version.
*/


//admin menu
function si_admin()
{
    if (function_exists('add_options_page')) {
        add_options_page('sitemap-index', 'Sitemap Index', 1, basename(__file__),
            'si_admin_panel');
    }
}

function si_admin_panel()
{

    //Add options if first time running
    add_option('si_order', 'date_descending', 'Sitemap Index Plugin');
    add_option('si_hidden', 'false', 'Sitemap Index Plugin');
    add_option('si_pages', 'on', 'Sitemap Index Plugin');
    add_option('si_tags', 'on', 'Sitemap Index Plugin');
    add_option('si_categ', 'on', 'Sitemap Index Plugin');
    add_option('si_links', '100', 'Sitemap Index Plugin');

    //get posted options
    $orderby = $_POST['orderby'];

    if (isset($_POST['info_update'])) {
        //update settings
        $orderby = $_POST['orderby'];
        $showhidden = $_POST['showhidden'];
        $showpages = $_POST['showpages'];
    	$showtags = $_POST['showtags'];
    	$showcateg = $_POST['showcateg'];
        $manylinks = $_POST['manylinks'];

        update_option('si_order', $orderby);
        update_option('si_hidden', $showhidden);
        update_option('si_pages', $showpages);
		update_option('si_tags', $showtags);
		update_option('si_categ', $showcateg);
        update_option('si_links', $manylinks);
    } else {
        //load settings from database
        $orderby = get_option('si_order');
        $showhidden = get_option('si_hidden');
        $showpages = get_option('si_pages');
		$showtags = get_option('si_tags');
		$showcateg = get_option('si_categ');
        $manylinks = get_option('si_links');
    }

$siteurl = get_option('siteurl');
$adres_mapy = $siteurl.'/wp-content/plugins/sitemap-index/gen_sitemap.php';

$added_sitemap = '';


function getPage($pageUrl, $maxTime)
{
    $curl = curl_init($pageUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, $maxTime);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}



function sitemapSubmit($strona,$engine,$OKmessage,$NOmessage)
{
	
	$okTag = 'OKsi';
	$noTag = 'NOsi';
	
	$pingurl = $engine.$strona;

	$source = @file_get_contents($pingurl);

	if ($source == false) {
		$source = getPage($pingurl,60);
		}

	if ($source != false) {
		
		$source = strip_tags($source);
		$source = "BUFOR".$source;
		
		$isOKmessage = stripos($source,$OKmessage);
		$isNOmessage = stripos($source,$NOmessage);
		
		if (($isOKmessage != false)&&($isNOmessage == false)) {$submitRaport = $okTag.$OKmessage;}
		if (($isOKmessage == false)&&($isNOmessage != false)) {$submitRaport = $noTag.$NOmessage;}
		if (($isOKmessage == false)&&($isNOmessage == false)) {$submitRaport = $noTag.'Submission error';}

	}
	
	else if ($source == false) {$submitRaport = $noTag.'Engine error';}
	
	return array($source, $submitRaport);

}

$silnik = array(

	'goo' => array (
		'nazwaEngine' => 'Google',
		'engine' => 'http://www.google.com/webmasters/sitemaps/ping?sitemap=',
		'OKmessage' => 'Sitemap Notification Received',
		'NOmessage' => 'Bad Request'
	),

	'bin' => array (
		'nazwaEngine' => 'Bing',
		'engine' => 'http://www.bing.com/webmaster/ping.aspx?siteMap=',
		'OKmessage' => 'Thanks for submitting your Sitemap.',
		'NOmessage' => 'baddddddd'
	)

);

/* old submision entries
$silnik = array(

	'goo' => array (
		'nazwaEngine' => 'Google',
		'engine' => 'http://www.google.com/webmasters/sitemaps/ping?sitemap=',
		'OKmessage' => 'Sitemap Notification Received',
		'NOmessage' => 'Bad Request'
	),

	'yah' => array (
		'nazwaEngine' => 'Yahoo!',
		'engine' => 'http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=',
		'OKmessage' => 'Update notification has successfully submitted',
		'NOmessage' => 'The following errors were detected'
	),	

	'liv' => array (
		'nazwaEngine' => 'Live Search',
		'engine' => 'http://webmaster.live.com/ping.aspx?siteMap=',
		'OKmessage' => 'Thanks for submitting your sitemap',
		'NOmessage' => 'baddddddd'
	),

	'ask' => array (
		'nazwaEngine' => 'Ask.com',
		'engine' => 'http://submissions.ask.com/ping?sitemap=',
		'OKmessage' => 'Your Sitemap submission was successful',
		'NOmessage' => 'Your Sitemap submission was not successful'
	),

	'mor' => array (
		'nazwaEngine' => 'Moreover',
		'engine' => 'http://api.moreover.com/ping?u=',
		'OKmessage' => 'Thank you for your ping',
		'NOmessage' => 'baddddddd'
	),
	
	'for' => array (
		'nazwaEngine' => 'ForumBiznesu.eu',
		'engine' => 'http://www.forumbiznesu.eu/wordpress/submission.php?sitemap=',
		'OKmessage' => 'Sitemap submission successful',
		'NOmessage' => 'baddddddd'
	),

);
 */


$addSitemap = $_POST['add'];

if ($addSitemap == 'yes') {

$added_sitemap .= '<p>';

foreach ($silnik as $siln => $cecha )
{
	
$nazwaEngine	= $cecha['nazwaEngine'];
$engine			= $cecha['engine'];
$OKmessage		= $cecha['OKmessage'];
$NOmessage		= $cecha['NOmessage'];

$strona = $adres_mapy;

list ($source, $submitRaport) =  sitemapSubmit($strona,$engine,$OKmessage,$NOmessage);

$statusTag = substr($submitRaport,0,4);
if ($statusTag == 'OKsi') {
	$icon = '<img border="0" src="'.$siteurl.'/wp-admin/images/yes.png" /> ';
	$alter_link = '';
	}
else if ($statusTag == 'NOsi') {
	$icon = '<img border="0" src="'.$siteurl.'/wp-admin/images/no.png" /> ';
	$alter_link = '<a href="'.$engine.$strona.'" target="_blank">Try manually</a><br />';
	}
else {
	$icon = '';
	$alter_link = '';
	}

$submitRaport = substr($submitRaport,4);

$insert_sitemap = "\n".$icon."<b>".$nazwaEngine."</b> reported:<br /><i>".$submitRaport."</i><br />".$alter_link;
$added_sitemap .= $insert_sitemap;

}

$added_sitemap .= '</p>';

}




?>

<div class="wrap">
<h2>Sitemap Index Settings</h2>
	<div>
		<p>
			Plugin version: <b>1.2.3</b>
		</p>
		<p>
			Sitemap URL address:<br /><a href="<?= $adres_mapy ?>" target="_blank"><?= $adres_mapy ?></a>
		</p>
	</div>
	<div style="float: left; margin-right: 10px; width: 250px">
		<form method="post">
		
				<h3>General settings:</h3>
				<p>
					<label>
						Links per sitemap: <input type="text" value="<?= $manylinks ?>" name="manylinks" size="6" />
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="showpages" <?php checked('on', $showpages); ?> class="tog"/> Insert pages
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="showtags" <?php checked('on', $showtags); ?> class="tog"/> Insert tags
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" name="showcateg" <?php checked('on', $showcateg); ?> class="tog"/> Insert categories
					</label>
				</p>
				
				<h3>Order links by:</h3>

				<p>
					<label>
						<input name="orderby" type="radio" value="date_descending" <?php checked('date_descending',
$orderby); ?> class="tog"/> Date, descending (Default)
					</label>
				</p>

				<p>
					<label>
						<input name="orderby" type="radio" value="date_ascending" <?php checked('date_ascending',
$orderby); ?> class="tog"/> Date, ascending
					</label>
				</p>

				<p>
					<label>
						<input name="orderby" type="radio" value="alpha_descending" <?php checked('alpha_descending',
$orderby); ?> class="tog"/> Alphabetical, descending
					</label>
				</p>

				<p>
					<label>
						<input name="orderby" type="radio" value="alpha_ascending" <?php checked('alpha_ascending',
$orderby); ?> class="tog"/> Alphabetical, ascending
					</label>
				</p>

				<h3>Hidden posts:</h3>

				<p>
					<label>
						<input name="showhidden" type="checkbox" <?php checked('on', $showhidden); ?> class="tog"/> Show hidden posts &amp; pages
					</label>
				</p>

			<div>
			<p class="submit">
				<input class="button-primary" type="submit" name="info_update" value="<?php _e('Save Changes') ?>" />
			</p>
			</div>
		</form>
	</div>
<div style="float: left; width: 280px; margin-right: 10px;">

<h3>Submit Sitemap</h3>
<span class="setting-description">This function submits created sitemap to the most popular search engines (Google, Bing).</span>
<form method="post">
<input type="hidden" name="add" value="yes" />
<p class="submit">
<input type="submit" name="add_sitemap" value="Submit Sitemap" />
</p>
</form>
<?=$added_sitemap?>
<h3>About:</h3>
<p>
<a href="http://twardes0.webd.pl/wordpress/sitemap-index-plugin" target="_blank">Sitemap Index Home Page</a>
</p>
<p>
<a href="https://wordpress.org/plugins/sitemap-index/" target="_blank">Sitemap Index at Wordpress.org</a>
</p>
<p>
<a href="http://www.atmail.pl/" target="_blank">Author's Home Page</a>
</p>
<p>
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=QJFMUHRMKC3KG&amp;lc=US&amp;item_name=Sitemap%20Index%20Plugin%20for%20Wordpress%20by%20Twardes&amp;item_number=si&amp;currency_code=USD&amp;bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank"><img class="aligncenter" title="Make a donation" src="http://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="" width="122" height="47" /></a>
</p>

</div>
</div>

	<?php
}


function si_plugin_actions($links, $file){
	static $this_plugin;

	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=sitemap-index.php">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}



//hooks
add_action('admin_menu', 'si_admin');
add_filter('plugin_action_links', 'si_plugin_actions', 10, 2);
?>