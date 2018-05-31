<?php

if( ! function_exists( 'model_post_type' ) ) :
	function model_post_type() {
		$args = array(
			'labels' => array(
  			'name' => '3D Models',
  			'singular_name' => '3D Model',
  			'add_new' => 'Add 3D Model',
  			'all_items' => 'All 3D Models',
  			'add_new_item' => 'Add 3D Model',
  			'edit_item' => 'Edit 3D Model',
  			'new_item' => 'New 3D Model',
  			'view_item' => 'View 3D Model',
  			'search_items' => 'Search 3D Models',
  			'not_found' => 'No 3D Models found',
  			'not_found_in_trash' => 'No 3D Models found in trash',
  			'parent_item_colon' => 'Parent 3D Model'
  			//'menu_name' => default to 'name'
  		),
			'public' => true,
			'has_archive' => true,
			'publicly_queryable' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array(
				'title',
				'editor',
				//'excerpt',
				'thumbnail',
				//'author',
				'trackbacks',
				//'custom-fields',
				'revisions',
				//'page-attributes',
				//'post-formats',
			),
			'taxonomies' => array( 'category', 'post_tag' ),
			'menu_position' => 5,
			'exclude_from_search' => false,
			'register_meta_box_cb' => 'model_add_post_type_metabox'
		);
		register_post_type( 'model', $args );

		register_taxonomy( 'model_category',
			'model',
			array(
				'hierarchical' => true,
				'labels' => array(
					'name' => '3D Models category',
					'singular_name' => '3D Model category',
				)
			)
		);
		register_taxonomy( 'model_tag',
			'model',
			array(
				'hierarchical' => false,
				'labels' => array(
					'name' => 'Model tag',
					'singular_name' => 'Model tag',
				)
			)
		);
	}
	add_action( 'init', 'model_post_type' );


	function model_add_post_type_metabox() {
		add_meta_box( 'model_metabox', 'Model', 'model_metabox', 'model', 'normal' );
	}


	function model_metabox() {
		global $post;

		echo '<input type="hidden" name="model_post_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

		$model_post_title = get_post_meta($post->ID, '_model_post_title', true);
		$model_post_json = get_post_meta($post->ID, '_model_post_json', true);

		?>

		<table class="form-table">
			<tr>
				<th>
					<label>Model Title</label>
				</th>
				<td>
					<input type="text" name="model_post_title" class="regular-text" value="<?php echo $model_post_title; ?>">
				</td>
			</tr>
			<tr>
				<th>
					<label>Model JSON</label>
				</th>
				<td>
          <p>example : <code>{"title":"","models":[{"url":"","title":"","author":""}]}</code> </p>
					<textarea name="model_post_json" class="large-text"><?php echo $model_post_json; ?></textarea>
				</td>
			</tr>
		</table>
	<?php
	}


	function model_post_save_meta( $post_id, $post ) {

		if ( ! isset( $_POST['model_post_noncename'] ) ) {
			return;
		}

		if( !wp_verify_nonce( $_POST['model_post_noncename'], plugin_basename(__FILE__) ) ) {
			return $post->ID;
		}

		if( ! current_user_can( 'edit_post', $post->ID )){
			return $post->ID;
		}

		$model_post_meta['_model_post_title'] = $_POST['model_post_title'];
		$model_post_meta['_model_post_json'] = $_POST['model_post_json'];

		foreach( $model_post_meta as $key => $value ) {

			$value = implode(',', (array)$value);
			if( get_post_meta( $post->ID, $key, FALSE ) ) {
				update_post_meta($post->ID, $key, $value);
			} else {
				add_post_meta( $post->ID, $key, $value );
			}
			if( !$value ) { // delete if blank
				delete_post_meta( $post->ID, $key );
			}
		}
	}
	add_action( 'save_post', 'model_post_save_meta', 1, 2 );
endif;

add_filter('sage/template/model/data', function (array $data) {
    $data['model_title'] = get_field('model_post_title');
    $data['model_json'] = get_field('model_post_json');
    return $data;
});

function add_post_types_to_query( $query ) {
  if ( is_home() && $query->is_main_query() )
    $query->set( 'post_type', array( 'post', 'model' ) );
  return $query;
}
add_action( 'pre_get_posts', 'add_post_types_to_query' );

?>
