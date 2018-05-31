# [MaterializeX](https://domnoble.com)
MaterializeX is a WordPress theme based on Sage from roots.io, implementing three.js, video.js, prism, materializecss and a some custom scripts. Also included are admin menus for changing classes and modifying sass variables from within wordpress, for rapid styling.

## Features
* Sass for stylesheets
* ES6 for JavaScript
* [Webpack](https://webpack.github.io/) for compiling assets, optimizing images, and concatenating and minifying files
* [Browsersync](http://www.browsersync.io/) for synchronized browser testing
* [Laravel's Blade](https://laravel.com/docs/5.3/blade) as a templating engine
* CSS framework:
  * [materializecss](https://materializecss.com/)
* JS frameworks:
  * [three.js](https://threejs.org/)
  * [video.js](http://videojs.com/)
  * [prism.js](https://prismjs.com/)

## Requirements

Make sure all dependencies have been installed before moving on:

* [WordPress](https://wordpress.org/) >= 4.7
* [PHP](http://php.net/manual/en/install.php) >= 5.6.4
* [Composer](https://getcomposer.org/download/)
* [Node.js](http://nodejs.org/) >= 6.9.x
* [Yarn](https://yarnpkg.com/en/docs/install)

Alternatively there is also a docker container available which will setup wordpress with persistance, however you will still need to have all the dependencies bar wordpress installed on the host

* [Wordpress Docker](https://github.com/domnoble/docker-wordpress/)

## Theme structure

```shell
themes/matx/              # → Root of theme
├── app/                  # → Theme PHP
│   ├── lib/Sage/         # → Blade implementation, asset manifest
│   ├── breadcrumbs.php   # → Breadcrumbs functions
│   ├── comment_form.php  # → Comment form functions
│   ├── comment_list.php  # → Comment list functions
│   ├── customizer.php    # → Theme customizer setup
│   ├── filters.php       # → Theme filters
│   ├── helpers.php       # → Helper functions
│   ├── navigation.php    # → Navigation walker
│   ├── options.php       # → Admin options controllers
│   ├── pagination.php    # → Pagination functions
│   ├── post_types.php    # → 3D model post type functions
│   ├── setup.php         # → Theme setup
│   └── tags.php          # → Tag cloud functions
├── composer.json         # → Autoloading for `app/` files
├── composer.lock         # → Composer lock file (never edit)
├── dist/                 # → Built theme assets (never edit)
├── node_modules/         # → Node.js packages (never edit)
├── package.json          # → Node.js dependencies and scripts
├── resources/            # → Theme assets and templates
│   ├── assets/           # → Front-end assets
│   │   ├── config.json   # → Settings for compiled assets
│   │   ├── build/        # → Webpack and ESLint config
│   │   ├── fonts/        # → Theme fonts
│   │   ├── images/       # → Theme images
│   │   ├── scripts/      # → Theme JS
│   │   └── styles/       # → Theme stylesheets
│   ├── functions.php     # → Composer autoloader, theme includes
│   ├── index.php         # → Never manually edit
│   ├── screenshot.png    # → Theme screenshot for WP admin
│   ├── style.css         # → Theme meta information
│   └── views/            # → Theme templates
│       ├── layouts/      # → Base templates
│       └── partials/     # → Partial templates
└── vendor/               # → Composer packages (never edit)
```

## Theme setup

Edit `app/setup.php` to enable or disable theme features, setup navigation menus, post thumbnail sizes, and sidebars.

* `composer install` — Intall php assets
* `npm install` — Install JavaScript assets

Alternatively you can use yarn to install assets...

* `yarn install` — Install JavaScript assets with yarn

### Development

The theme container itself builds without composer or node installed, in order to run

### Build commands

* `yarn run start` — Compile assets when file changes are made, start Browsersync session
* `yarn run build` — Compile and optimize the files in your assets directory
* `yarn run build:production` — Compile assets for production
