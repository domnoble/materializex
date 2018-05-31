<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }


    $forbidden = "parallax";
    if(in_array($forbidden, $classes)){
      if(($key = array_search($forbidden, $classes)) !== false) {
        unset($classes[$key]);
      }
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Custom Search Form
 */
add_filter('get_search_form', function(){
  $form = '';
  echo template(realpath(config('dir.template') . '/views/partials/searchform.blade.php'), []);
  return $form;
});

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", function ($templates) {
        return collect($templates)->flatMap(function ($template) {
            $transforms = [
                '%^/?(resources[\\/]views)?[\\/]?%' => '',
                '%(\.blade)?(\.php)?$%' => ''
            ];
            $normalizedTemplate = preg_replace(array_keys($transforms), array_values($transforms), $template);
            return ["{$normalizedTemplate}.blade.php", "{$normalizedTemplate}.php"];
        })->toArray();
    });
});

/**
 * Render Sidebar using sage
 */
 add_filter('sage/display_sidebar', function ($display) {
     static $display;

     isset($display) || $display = in_array(true, [
       // The sidebar will be displayed if any of the following return true
       is_single(),
       is_404()
     ]);

     return $display;
 });


/**
 * Add extra file types to media upload.
 */
add_filter('upload_mimes',function( $mimes ) {
  // New allowed mime types.
  $mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
  $mimes['doc'] = 'application/msword';
  $mimes['step'] = 'application/step';
  $mimes['stp'] = 'application/step';
  $mimes['glb'] = 'model/gltf-binary';
  $mimes['gltf'] = 'model/gltf-binary';
  //$mimes['stl'] = 'application/sla';
  //$mimes['stl'] = 'application/vnd.ms-pki.stl';
  $mimes['stl'] = 'application/x-navistyle';
  //$mimes['iges'] = 'model/iges';
  $mimes['iges'] = 'application/iges';
  //$mimes['igs'] = 'model/iges';
  $mimes['igs'] = 'application/iges';
  // Optional. Remove a mime type.
  unset( $mimes['exe'] );

	return $mimes;
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    echo template($template, $data);
    // Return a blank file to make WordPress happy
    return get_theme_file_path('index.php');
}, PHP_INT_MAX);

/**
 * Tell WordPress how to find the compiled path of comments.blade.php
 */
add_filter('comments_template', 'App\\template_path');

/**
 * Tell WordPress not to add p tags to everything
 */
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );
