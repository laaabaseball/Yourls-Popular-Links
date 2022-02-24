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
if (!defined('YOURLS_ABSPATH')) die();

yourls_add_action('plugins_loaded', 'popularlinks_add_page');
function popularlinks_add_page() {
   yourls_register_plugin_page('popular_links', 'Popular Links', 'popularlinks_do_page');
}
// Display popular links.
function popularlinks_do_page() {
   $nonce = yourls_create_nonce('popular_links');
   echo '<style type="text/css">
	    .popular-links {
                border-color: #313131;
                border-collapse: collapse;
	    }
	    .popular-links th {
                font-size: 12px;
                color: white;
            }
            .popular-links td, th {
                padding: 8px;
                border-color: #313131;
                text-align: center;
           }
            .popular-links thead {
                background-color: #007bff;
            }
            .popular-links td:nth-child(1) {
                font-weight: 700;
                background: -webkit-linear-gradient(left, #2db86cba  var(--percentage), transparent var(--percentage));
                background: -moz-linear-gradient(left, #2db86cba  var(--percentage), transparent var(--percentage));
                background: -ms-linear-gradient(left, #2db86cba  var(--percentage), transparent var(--percentage));
                background: -o-linear-gradient(left, #2db86cba  var(--percentage), transparent var(--percentage));
                background: linear-gradient(to right, #2db86cba  var(--percentage), transparent var(--percentage));
            }
           .popular-links tbody tr:hover {
                background: rgb(90, 90, 90); /* fallback */
                background: rgba(90, 90, 90, 0.1);
           }
           .url {
                display: inline-grid;
            }
        </style>
        <h2>Popular Links</h2>';

   function show_top($numdays, $numlinks) {
      global $ydb;
      $table_url = YOURLS_DB_TABLE_URL;
      $base      = YOURLS_SITE;
      $links     = '';

      $query = $ydb->fetchObjects(
         "SELECT `title`, `timestamp`, `url`, `keyword`, `clicks` FROM `$table_url`
	                             WHERE `timestamp` >= SUBDATE(CURDATE(), $numdays)
	                             ORDER BY `clicks` DESC
	                             LIMIT $numlinks"
      );
      if ($query) {

         $maxClicks = max(array_column($query, 'clicks'));

         foreach ($query as $query_result) {
            if ($query_result->clicks > 0) {
               $thisURLArray = parse_url(stripslashes($query_result->url));
               $diff = abs(time() - strtotime($query_result->timestamp));
               $days = floor($diff / (60 * 60 * 24));
               if ($days < 1) {
                  $created = 'today';
               } else if ($days < 2) {
                  $created = ' 1 day ago';
               } else {
                  $created = $days . ' days ago';
               }

               $percentage = ($query_result->clicks / $maxClicks) * 100 + 1;

               $links .=  '<tr>
                              <td style="--percentage:' . $percentage . '%">' . $query_result->clicks . '</td>
                              <td>' . $created . '</td>
                              <td><a href="' . $base . '/' . $query_result->keyword . '" target="blank">' . $query_result->keyword . '</a></td>
                              <td>
                                 <div class="url">
                                    <span>' . str_replace('www.', '', $query_result->title) . '</span>
                                    <a href="' . $query_result->url . '" target="blank">' . $query_result->url . '</a>
                                 </div>
                              </td>
                              <td>' . $thisURLArray['host'] . '</td>
                           </tr>';
            }
         }
      }
      echo '<h3><b>Last ' . $numdays . ' Days:</b></h3><br> 
               <table class="popular-links" border="1">
                  <thead>
                     <tr>
                        <th>Clicks</th>
                        <th>Created</th>
                        <th>Short Url (Keyword)</th>
                        <th>Original Url</th>
                        <th>Host</th>
                     </tr>
                  </thead>
                  <tbody>'
                     . $links .
                  '</tbody>
               </table>'
         . "<br><br>\n\r";
   }
   
   // Edit these if you want to show a different number of days/number of links! i.e. 1,5 means the 5 most popular links created in the last 1 day.
   show_top(1, 5);
   show_top(7, 5);
   show_top(30, 5);
   show_top(365, 5);
}
