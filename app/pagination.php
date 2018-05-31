<?php

namespace App;

/**
 * Get the post pagination.
 *
 * @param array $args
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function mx_link_pages( $args = '' ) {
    global $page, $numpages, $multipage, $more;

    $defaults = array(
        'before'             => '<ul class="pagination">',
        'after'              => '</ul>',
        'link_before'        => '<li class="waves-effect">',
        'link_after'         => '</li>',
        'active_link_before' => '<li class="active">',
        'active_link_after'  => '</li>',
        'next_or_number'     => 'number',
        'separator'          => ' ',
        'nextpagelink'       => __( 'Next page' ),
        'previouspagelink'   => __( 'Previous page' ),
        'pagelink'           => '%',
        'echo'               => 1
    );

    $params = wp_parse_args( $args, $defaults );

    /**
     * Filters the arguments used in retrieving page links for paginated posts.
     *
     * @since 3.0.0
     *
     * @param array $params An array of arguments for page links for paginated posts.
     */
    $r = apply_filters( 'wp_link_pages_args', $params );

    $output = '';
    if ( $multipage ) {
        if ( 'number' == $r['next_or_number'] ) {
            $output .= $r['before'];
            for ( $i = 1; $i <= $numpages; $i++ ) {
                $link = str_replace( '%', $i, $r['pagelink'] );
                if ( $i != $page || ! $more && 1 == $page ) {
                    $link = $r['link_before'] . _mx_link_page( $i , '') . $link . '</a>' . $r['link_after'];
                }else{
                   $link = $r['active_link_before'] . _mx_link_page( $i , 'active') . $link . '</a>' . $r['active_link_after'];
                }
                /**
                 * Filters the HTML output of individual page number links.
                 *
                 * @since 3.6.0
                 *
                 * @param string $link The page number HTML output.
                 * @param int    $i    Page number for paginated posts' page links.
                 */
                $link = apply_filters( 'wp_link_pages_link', $link, $i );

                // Use the custom links separator beginning with the second link.
                $output .= ( 1 === $i ) ? ' ' : $r['separator'];
                $output .= $link;
            }
            $output .= $r['after'];
        } elseif ( $more ) {
            $output .= $r['before'];
            $prev = $page - 1;
            if ( $prev > 0 ) {
                $link =  $r['link_before'] . _mx_link_page( $prev , ''  ) . $r['previouspagelink'] . $r['link_after'] . '</a>';

                /** This filter is documented in wp-includes/post-template.php */
                $output .= apply_filters( 'wp_link_pages_link', $link, $prev );
            }
            $next = $page + 1;
            if ( $next <= $numpages ) {
                if ( $prev ) {
                    $output .= $r['separator'];
                }
                $link = $r['link_before'] . _mx_link_page( $next , '' ) . $r['nextpagelink'] . $r['link_after'] . '</a>';

                /** This filter is documented in wp-includes/post-template.php */
                $output .= apply_filters( 'wp_link_pages_link', $link, $next );
            }
            $output .= $r['after'];
        }
    }

    /**
     * Filters the HTML output of page links for paginated posts.
     *
     * @since 3.6.0
     *
     * @param string $output HTML output of paginated posts' page links.
     * @param array  $args   An array of arguments.
     */
    $html = apply_filters( 'wp_link_pages', $output, $args );

    if ( $r['echo'] ) {
        echo $html;
    }
    return $html;
}

/**
 * Get the page link.
 *
 * @param integer $i
 * @param string $class
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function _mx_link_page( $i , $class = '' ) {
    global $wp_rewrite;
    $post = get_post();
    $query_args = array();

    if ( 1 == $i ) {
        $url = get_permalink();
    } else {
        if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
            $url = add_query_arg( 'page', $i, get_permalink() );
        elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
            $url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
        else
            $url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
    }

    if ( is_preview() ) {

        if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
            $query_args['preview_id'] = wp_unslash( $_GET['preview_id'] );
            $query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
        }

        $url = get_preview_post_link( $post, $query_args, $url );
    }

    return '<a href="' . esc_url( $url ) . '" class="'.$class.'" >';
}

/**
 * Get the main pagination.
 *
 * @param array $args
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function mx_get_the_posts_navigation( $args = array() ) {
    global $paged;
    $navigation = '';

    // Don't print empty markup if there's only one page.
    if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {
        $params = wp_parse_args( $args, array(
          'before'             => '<ul class="pagination">',
          'after'              => '</ul>',
          'link_before'        => '<li class="waves-effect">',
          'link_after'         => '</li>',
          'active_link_before' => '<li class="active">',
          'active_link_after'  => '</li>',
          'next_or_number'     => 'number',
          'attr'               => ' ',
          'separator'          => ' ',
          'prev_text'          => '<i class="material-icons">chevron_left</i>',
          'next_text'          => '<i class="material-icons">chevron_right</i>',
          'pagelink'           => '%',
          'screen_reader_text' => __( 'Posts navigation' ),
          'echo'               => 1
        ) );

        /**
         * Filters the arguments used in retrieving page links for paginated posts.
         *
         * @since 3.0.0
         *
         * @param array $params An array of arguments for page links for paginated posts.
         */
        $r = apply_filters( 'mx_post_navigation_args', $params );


        $prev_link = get_previous_posts_link( $r['prev_text'] );
        $next_link = get_next_posts_link( $r['next_text'] );

        if ( $prev_link ) {
            $navigation .= $r['link_before'] . $prev_link . $r['link_after'];
        }

        for ($i = 1; $i <= $GLOBALS['wp_query']->max_num_pages; $i++) {
           if($paged < 1){
             $paged = 1;
           }
           $attr = $r['attr'];

           if($paged == $i){
             $link = '<a href="#!">'. preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $i ) .'</a>';
              $navigation .= $r['active_link_before'] . $link . $r['active_link_after'] . " ";
           }else{
             $link = '<a href="' . get_pagenum_link($i) . "\" $attr>". preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $i ) .'</a>';
              $navigation .= $r['link_before'] . $link . $r['link_after'] . " ";
           }
        }

        if ( $next_link ) {
            $navigation .= $r['link_before'] . $next_link . $r['link_after'];
        }

        $navigation = mx_navigation_markup( $navigation, 'posts-navigation', $r['screen_reader_text'] );
    }

    return $navigation;
}


/**
 * Main Pagination Wrapper.
 *
 * @param string $links
 * @param string $class
 * @param string $screen_reader_text
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function mx_navigation_markup( $links, $class = 'posts-navigation', $screen_reader_text = '' ) {
    if ( empty( $screen_reader_text ) ) {
        $screen_reader_text = __( 'Posts navigation' );
    }

    $template = '
    <div class="page-num-nav center-align" role="navigation">
        <span class="screen-reader-text">%2$s</span>
        <ul class="pagination">%3$s</ul>
    </div>';

    /**
     * Filters the navigation markup template.
     *
     * Note: The filtered template HTML must contain specifiers for the navigation
     * class (%1$s), the screen-reader-text value (%2$s), and placement of the
     * navigation links (%3$s):
     *
     *     <nav class="navigation %1$s" role="navigation">
     *         <h2 class="screen-reader-text">%2$s</h2>
     *         <div class="nav-links">%3$s</div>
     *     </nav>
     *
     * @since 4.4.0
     *
     * @param string $template The default template.
     * @param string $class    The class passed by the calling function.
     * @return string Navigation template.
     */
    $template = apply_filters( 'navigation_markup_template', $template, $class );

    return sprintf( $template, sanitize_html_class( $class ), esc_html( $screen_reader_text ), $links );
}


?>
