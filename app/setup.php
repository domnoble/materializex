<?php

namespace App;

use Illuminate\Contracts\Container\Container as ContainerContract;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Config;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);
    wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);
}, 100);

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage')
    ]);

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support( 'post-thumbnails', array( 'post', 'models' ) );

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    add_editor_style(asset_path('styles/main.css'));
}, 20);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('Primary', 'sage'),
        'id'            => 'sidebar-primary',
        'before_widget' => '<section class="widget card %1$s %2$s '.get_option( 'mx_card_color' ).'"><div class="card-content">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3 class="header card-title">',
        'after_title'   => '</h3>'
    ]);
    register_sidebar([
        'name'          => __('Footer', 'sage'),
        'id'            => 'sidebar-footer',
        'before_widget' => '<section class="widget %1$s %2$s col s6 white-text">',
        'after_widget'  => '</section>',
        'before_title'  => '<h5 class="header">',
        'after_title'   => '</h5>'
    ]);
});

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    sage('blade')->share('post', $post);
});


/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Sage config
     */
    $paths = [
        'dir.stylesheet' => get_stylesheet_directory(),
        'dir.template'   => get_template_directory(),
        'dir.upload'     => wp_upload_dir()['basedir'],
        'uri.stylesheet' => get_stylesheet_directory_uri(),
        'uri.template'   => get_template_directory_uri(),
    ];
    $viewPaths = collect(preg_replace('%[\/]?(resources/views)?[\/.]*?$%', '', [STYLESHEETPATH, TEMPLATEPATH]))
        ->flatMap(function ($path) {
            return ["{$path}/resources/views", $path];
        })->unique()->toArray();

    config([
        'assets.manifest' => "{$paths['dir.stylesheet']}/../dist/assets.json",
        'assets.uri'      => "{$paths['uri.stylesheet']}/dist",
        'view.compiled'   => "{$paths['dir.upload']}/cache/compiled",
        'view.namespaces' => ['App' => WP_CONTENT_DIR],
        'view.paths'      => $viewPaths,
    ] + $paths);

    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

    /**
     * Add Blade to Sage container
     */
    sage()->singleton('sage.blade', function (ContainerContract $app) {
        $cachePath = config('view.compiled');
        if (!file_exists($cachePath)) {
            wp_mkdir_p($cachePath);
        }
        (new BladeProvider($app))->register();
        return new Blade($app['view'], $app);
    });

    /**
     * Add shortcode_tags to blade views
     */
    sage('blade')->share('shortcodes', $GLOBALS['shortcode_tags']);

    /**
     * Create @asset() Blade directive
     */
    sage('blade')->compiler()->directive('asset', function ($asset) {
        return "<?= App\\asset_path({$asset}); ?>";
    });


    /**
     * Advanced Custom Fields Blade directives taken from <https://discourse.roots.io/t/best-practice-resources-for-blade/8341/38>
     */

    /**
     * Create @fields() Blade directive
     */
    sage('blade')->compiler()->directive('fields', function ($expression) {
        $expression = strtr($expression, array('(' => '', ')' => ''));
        $output = "<?php if (have_rows($expression)) : ?>";
        $output .= "<?php while (have_rows($expression)) : ?>";
        $output .= "<?php the_row(); ?>";
        return $output;
    });

    /**
     * Create @endFields Blade directive
     */
    sage('blade')->compiler()->directive('endFields', function () {
        return "<?php endwhile; endif; ?>";
    });

    /**
     * Create @field() Blade directive
     */
    sage('blade')->compiler()->directive('field', function ($expression) {
         $expression = strtr($expression, array('(' => '', ')' => ''));
         return "<?php the_field($expression); ?>";
    });

    /**
     * Create @getField() Blade directive
     */
    sage('blade')->compiler()->directive('getField', function ($expression) {
        $expression = strtr($expression, array('(' => '', ')' => ''));
        return "<?php get_field($expression); ?>";
    });

    /**
     * Create @hasField() Blade directive
     */
    sage('blade')->compiler()->directive('hasField', function ($expression) {
        $expression = strtr($expression, array('(' => '', ')' => ''));
        return "<?php if (get_field($expression)) : ?>";
    });

    /**
     * Create @endField Blade directive
     */
    sage('blade')->compiler()->directive('endField', function () {
        return "<?php endif; ?>";
    });

    /**
     * Create @sub() Blade directive
     */
    sage('blade')->compiler()->directive('sub', function ($expression) {
        $expression = strtr($expression, array('(' => '', ')' => ''));
        return "<?php the_sub_field($expression); ?>";
    });

    /**
     * Create @getSub() Blade directive
     */
    sage('blade')->compiler()->directive('getSub', function ($expression) {
        $expression = strtr($expression, array('(' => '', ')' => ''));
        return "<?php get_sub_field($expression); ?>";
    });

    /**
     * Create @hasSub() Blade directive
     */
    sage('blade')->compiler()->directive('hasSub', function ($expression) {
        $expression = strtr($expression, array('(' => '', ')' => ''));
        return "<?php if (get_sub_field($expression)) : ?>";
    });

    /**
     * Create @endSub Blade directive
     */
    sage('blade')->compiler()->directive('endSub', function () {
        return "<?php endif; ?>";
    });

});

/**
 * Init config
 */
sage()->bindIf('config', Config::class, true);
