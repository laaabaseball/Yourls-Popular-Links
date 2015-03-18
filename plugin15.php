<?php
/*
Plugin Name: Popular Links
Plugin URI: http://gkurl.us/
Description: Shows an admin page with the most popular links
Version: 1.0
Author: laaabaseball
Author URI: http://gkurl.us/laaa
*/

yourls_add_action( 'plugins_loaded', 'popularlinks_add_page' );
function popularlinks_add_page() {
        yourls_register_plugin_page( 'popular_links', 'Popular Links', 'popularlinks_do_page' );
}
// Display popular links
function popularlinks_do_page() {
        $nonce = yourls_create_nonce('popular_links');
        echo <<<HTML
		<h2>Popular Links</h2>
HTML;
function show_top($numdays,$numlinks) {
//credit where credit is due - based on hack by MrTech at http://www.mrtech.com/forums/index.php/topic,2524.0.html
//define stuff
global $ydb;
 $base  = YOURLS_SITE;
 $table_url = YOURLS_DB_TABLE_URL;
// go!
$urlt2 	= '';

$query = $ydb->get_results("SELECT title, timestamp, url, keyword, clicks FROM `$table_url` WHERE timestamp >= SUBDATE(CURDATE(), $numdays) order by clicks desc limit $numlinks");
if ($query) {
	foreach( $query as $query_result ) {
		$thisURLArray 	= parse_url(stripslashes($query_result->url));
		$diff = abs( time() - strtotime( $query_result->timestamp ) );
	    $days = floor( $diff / (60*60*24) );
	if( $days == 0 ) {
		$created = 'today';
	} else {
		$created = $days.' '.yourls_plural( 'day', $days).' ago';
	}
		$urlt2 		.= '(' . $query_result->clicks . ')  - ' .$thisURLArray[host] .' - <a href="' . $base . '/' . $query_result->keyword .'" target="blank">';
	
$urlt2 		.= str_replace('www.', '', $query_result->title) . '</a> <a href=" ' . $base . '/' . $query_result->keyword .'+" target="blank"></a> Created ' . $created . '<br/>';
	}
}
echo '<h3><b>Popular Links in the Last '. $numdays . ' Days:</b></h3><br/> ' . $urlt2 . "<br><br>\n\r";
}
//edit these if you want to show a different number of days/number of links! i.e. 1,5 = 5 most popular links created in the last 1 day
show_top(1,5);
show_top(30,5);
show_top(365,5);
show_top(1000,5);
}
