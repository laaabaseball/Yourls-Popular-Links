<?php

// Make sure we're in YOURLS context
if( !defined( 'YOURLS_ABSPATH' ) ) {
	// Attempt to guess URL via YOURLS
	$url = 'http://' . $_SERVER['HTTP_HOST'] . str_replace( array( '/pages/', '.php' ) , array ( '/', '' ), $_SERVER['REQUEST_URI'] );
	echo "Try this instead: <a href='$url'>$url</a>";
	die();
}

// Display page content. Any PHP, HTML and YOURLS function can go here.
$url = YOURLS_SITE . '/popularlinks';

yourls_html_head( 'popularlinks', 'Popular Links' );

// Start YOURLS engine
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/load-yourls.php' ;


 echo <<<HTML
		<h2>Popular Links</h2>
		
HTML;
function show_top($numdays,$numlinks) {

global $ydb;
 $base  = YOURLS_SITE;
 $table_url = YOURLS_DB_TABLE_URL;
// 
$links 	= '';

$query = $ydb->get_results("SELECT title, timestamp, url, keyword, clicks FROM `$table_url` WHERE timestamp >= SUBDATE(CURDATE(), $numdays) order by clicks desc limit $numlinks");
if ($query) {
	foreach( $query as $query_result ) {
		$thisURLArray 	= parse_url(stripslashes($query_result->url));
		$diff = abs( time() - strtotime( $query_result->timestamp ) );
	    $days = floor( $diff / (60*60*24) );
	if( $days <1) {
		$created = 'today';
	} 
	else if( $days <2) {
		$created = ' 1 day ago';
	}
	else  {
		$created = $days. ' days ago';
	}
		$links 		.= '(' . $query_result->clicks . ')  - ' .$thisURLArray[host] .' - <a href="' . $base . '/' . $query_result->keyword .'" target="blank">';
	
$links 		.= str_replace('www.', '', $query_result->title) . '</a> <a href=" ' . $base . '/' . $query_result->keyword .'+" target="blank"></a> Created ' . $created . '<br/>';
	}
}
echo '<h3><b>Popular Links in the Last '. $numdays . ' Days:</b></h3><br/> ' . $links . "<br><br>\n\r";
}
//edit these if you want to show a different number of days/number of links! i.e. 1,5 = 5 most popular links created in the last 1 day
show_top(1,5);
show_top(30,5);
show_top(365,5);
show_top(1000,5);
yourls_html_footer();

?>
