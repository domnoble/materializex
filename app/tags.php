<?php

namespace App;

/**
 * Tag Cloud Filter
 */
 add_filter( 'wp_tag_cloud', function ( $return ) {
 	$return = str_replace('<a', '<a rel="nofollow" ', $return );
  $return = str_replace('class="', 'class="chip ', $return );
 	return $return;
});

/**
 * Generate Tag Cloud Filter
 */
 add_filter( 'wp_generate_tag_cloud', function ( $return ) {
  $return = str_replace('style="font-size: 1pt;"', '', $return ); // Remove Tag cloud font sizing
 	return $return;
},'', array(
  'filter' => true,
));

/**
 * Generate Tag Cloud Filter
 */
 add_filter( 'wp_generate_tag_cloud_data', function ( $tag_data ) {
   for ($i = 0; $i < count($tag_data); $i++){
     $tag_data[$i]['font_size'] = 1; // Set the font size to a generic size so its easier to remove it in generate tag cloud filter
     $name = $tag_data[$i]['name'];
     $count = $tag_data[$i]['real_count'];
     $tag_data[$i]['name'] = $name . ' ' . $count;
   }
   return $tag_data;
 });


 ?>
