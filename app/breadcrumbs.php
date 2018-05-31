<?php

namespace App;

/**
 * Breadcrumbs
 *
 * @param array $args
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function mx_breadcrumbs($args = array()) {

    // Settings
    $params = wp_parse_args( $args, array(
      'seperator'       => '',
      'link_before'     => '',
      'link_after'      => '',
      'link_class'      => 'breadcrumb',
      'home_title'      => __('Home'),
      'content_before'  => '<nav class="'.get_option( 'mx_breadcrumbs_class' ).'"><div class="nav-wrapper"><div class="col s12">',
      'content_after'   => '</div></div></nav>',
      'custom_taxonomy' => ''   // If you have any custom post types with custom taxonomies(e.g. product_cat)
    ));

    /**
     * Filters the arguments used in retrieving page links for paginated posts.
     *
     * @since 3.0.0
     *
     * @param array $params An array of arguments for page links for paginated posts.
     */
    $r = apply_filters( 'mx_breadcrumbs_args', $params );


    // Get the query & post information
    global $post,$wp_query;

    $breadcrumbs = $r['content_before'];
    // Do not display on the homepage
    if ( !is_front_page() ) {

        // Build the breadcrums
        // Home page
        $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="' . get_home_url() . '" title="' . $r['home_title'] . '">' . $r['home_title'] . '</a>' . $r['link_after'];

        if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {

            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . post_type_archive_title($prefix, false) . '">' . post_type_archive_title($prefix, false) . '</a>' . $r['link_after'];

        } else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {

            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
                $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . " " . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a>' . $r['link_after'];

            }

            $custom_tax_name = get_queried_object()->name;
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . $custom_tax_name . '">' . $custom_tax_name . '</a>' . $r['link_after'];

        } else if ( is_single() ) {

            // If post is a custom post type
            $post_type = get_post_type();

            // If it is a custom post type display name and link
            if($post_type != 'post') {

                $post_type_object = get_post_type_object($post_type);
                $post_type_archive = get_post_type_archive_link($post_type);
                $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . " " . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a>' . $r['link_after'];

            }

            // Get post category info
            $category = get_the_category();

            if(!empty($category)) {
                // Get last category post is in

                // Get parent any categories and create array
                $get_cat_parents = rtrim(get_category_parents($category[0]->term_id, true, ','),',');
                $cat_parents = explode(',',$get_cat_parents);

                // Loop through parent categories and store in variable $cat_display
                if(count($cat_parents) < 2){
                    $breadcrumbs .= $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . $category[0]->name . '">' . $category[0]->name . '</a>' . $r['link_after'];
                }else{
                  foreach($cat_parents as $parents) {
                      $breadcrumbs .= $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . $parents . '">' . $parents . '</a>' . $r['link_after'];
                  }
                }
            }

            // If it's a custom post type within a custom taxonomy
            $taxonomy_exists = taxonomy_exists($r['custom_taxonomy']);
            if(empty($last_category) && !empty($r['custom_taxonomy']) && $taxonomy_exists) {
                $taxonomy_terms = get_the_terms( $post->ID, $r['custom_taxonomy'] );
                $cat_id         = $taxonomy_terms[0]->term_id;
                $cat_nicename   = $taxonomy_terms[0]->slug;
                $cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
                $cat_name       = $taxonomy_terms[0]->name;
            }

            // Check if the post is in a category
            if(!empty($last_category)) {
              $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_title() . '">' . get_the_title() . '</a>' . $r['link_after'];
            // Else if post is in a custom taxonomy
            } else if(!empty($cat_id)) {
              $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="' . $cat_link . '"  title="' . $cat_name . '">' . $cat_name . '</a>' . $r['link_after'];
              $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_title() . '">' . get_the_title() . '</a>' . $r['link_after'];
            } else {
              $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_title() . '">' . get_the_title() . '</a>' . $r['link_after'];
            }

        } else if ( is_category() ) {

            // Category page
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . single_cat_title('', false) . '">' . single_cat_title('', false) . '</a>' . $r['link_after'];

        } else if ( is_page() ) {

            // Standard page
            if( $post->post_parent ){

                // If child page, get parents
                $anc = get_post_ancestors( $post->ID );

                // Get parents in the right order
                $anc = array_reverse($anc);

                // Parent page loop
                if ( !isset( $parents ) ) $parents = null;
                foreach ( $anc as $ancestor ) {
                  $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a>' . $r['link_after'];
                }


                // Current page
                $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_title() . '">' . get_the_title() . '</a>' . $r['link_after'];

            } else {

                // Just display current page if not parents
                $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_title() . '">' . get_the_title() . '</a>' . $r['link_after'];

            }

        } else if ( is_tag() ) {

            // Tag page

            // Get tag information
            $term_id        = get_query_var('tag_id');
            $taxonomy       = 'post_tag';
            $args           = 'include=' . $term_id;
            $terms          = get_terms( $taxonomy, $args );
            $get_term_id    = $terms[0]->term_id;
            $get_term_slug  = $terms[0]->slug;
            $get_term_name  = $terms[0]->name;

            // Display the tag name
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . $get_term_name . '">' . $get_term_name . '</a>' . $r['link_after'];


        } elseif ( is_day() ) {

            // Year link
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . '</a>' . $r['link_after'];

            // Month link
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . '</a>' . $r['link_after'];

            // Day display
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_time('jS') . ' ' . get_the_time('M') . '">' . get_the_time('jS') . ' ' . get_the_time('M') . '</a>' . $r['link_after'];

        } else if ( is_month() ) {

            // Year link
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' </a>' . $r['link_after'];

            // Month display
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_time('M') . '">' . get_the_time('M') . ' </a>' . $r['link_after'];

        } else if ( is_year() ) {

            // Display year archive
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' </a>' . $r['link_after'];

        } else if ( is_author() ) {

            // Auhor archive

            // Get the author information
            global $author;
            $userdata = get_userdata( $author );

            // Display author name
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="' . $userdata->display_name . '">' . $userdata->display_name . ' </a>' . $r['link_after'];

        } else if ( get_query_var('paged') ) {

            // Paginated archives
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="'.__('Page') . ' ' . get_query_var('paged') . '">'.__('Page') . ' ' . get_query_var('paged') . ' </a>' . $r['link_after'];

        } else if ( is_search() ) {

            // Search results page
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . ' </a>' . $r['link_after'];

        } elseif ( is_404() ) {

            // 404 page
            $breadcrumbs .=  $r['link_before'] . '<a class="' . $r['link_class'] . '" href="#!" title="Error 404">Error 404</a>' . $r['link_after'];

        }

    }
    $breadcrumbs .= $r['content_after'];

    return $breadcrumbs;

}
