<?php
/*
Plugin Name: Popular Links
Plugin URI: https://github.com/laaabaseball/Yourls-Popular-Links
Description: Shows an admin page with the most popular links.
Version: 3.0
Author: laaabaseball
Author URI: http://kurtonium.com
*/

// No direct call.
if ( !defined( 'YOURLS_ABSPATH' ) ) die();

yourls_add_action( 'plugins_loaded', 'popularlinks_add_page' );
function popularlinks_add_page() {
        yourls_register_plugin_page( 'popular_links', 'Popular Links', 'popularlinks_do_page' );
}
// Display popular links.
function popularlinks_do_page() {
	$nonce = yourls_create_nonce('popular_links');
	echo '<h2>Popular Links</h2>';

	function show_top($numdays, $numlinks) {
	global $ydb;
	$table_url = YOURLS_DB_TABLE_URL;
	$base      = YOURLS_SITE;
	$links     = '';

	$query = $ydb->fetchObjects("SELECT `title`, `timestamp`, `url`, `keyword`, `clicks` FROM `$table_url`
	                             WHERE `timestamp` >= SUBDATE(CURDATE(), $numdays)
	                             ORDER BY `clicks` DESC
	                             LIMIT $numlinks"
	                           );
	if ($query) {
		foreach ( $query as $query_result ) {
			if ($query_result->clicks > 0) {
				$thisURLArray = parse_url(stripslashes($query_result->url));
				$diff = abs( time() - strtotime( $query_result->timestamp ) );
				$days = floor( $diff / (60 * 60 * 24) );
			if ( $days < 1) {
				$created = 'today';
			}
			else if ( $days < 2) {
				$created = ' 1 day ago';
			}
			else {
				$created = $days. ' days ago';
			}
				$links .= '(' . $query_result->clicks . ')  &ndash; ' .$thisURLArray['host'] .' &ndash; <a href="' . $base . '/' . $query_result->keyword .'" target="blank">';

		$links .= str_replace('www.', '', $query_result->title) . '</a> <a href=" ' . $base . '/' . $query_result->keyword .'+" target="blank"></a> &ndash; Created ' . $created . '<br>';
			}
		}
	}
	echo '<h3><b>Popular Links in the Last '. $numdays . ' Days:</b></h3><br> ' . $links . "<br><br>\n\r";
	}
	// Edit these if you want to show a different number of days/number of links! i.e. 1,5 means the 5 most popular links created in the last 1 day.
	show_top(1,5);
	show_top(7,5);
	show_top(30,5);
	show_top(365,5);
}
