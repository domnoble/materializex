// jshint esversion:6
/** import external dependencies */
import './jquery';
import 'three';
import 'materialize-css/dist/js/materialize';
import 'materialize-css/js/date_picker/picker';
import 'prismjs/prism';
import 'prismjs/components/prism-scss';
import 'prismjs/components/prism-docker';
import 'prismjs/components/prism-markdown';
import 'prismjs/components/prism-nginx';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-bash';

var Masonry = require('masonry-layout');
var jQueryBridget = require('jquery-bridget');
var imagesLoaded = require('imagesloaded');

/* eslint-disable no-unused-vars */
import videojs from 'video.js';
import ThreeD from './util/threeD';
window.ThreeD = ThreeD;
import Audio from "./util/Audio";
window.Audio = Audio;
window.videojs = videojs;
/* globals fetch, ThreeD, Audio, videojs, THREE */
/* eslint-enable no-unused-vars */

/** import local dependencies */
import Router from './util/Router';
import common from './routes/common';
import content from './routes/content';
import home from './routes/home';
import aboutUs from './routes/about';
import forms from './routes/forms';
import media from './routes/media';

jQueryBridget( 'masonry', Masonry, $ );

/**
 * Populate Router instance with DOM routes
 * @type {Router} routes - An instance of our router
 */
const routes = new Router({
  /** All pages */
  common,
  /** Home page */
  home,
  /** About Us page, note the change from about-us to aboutUs. */
  aboutUs,
  /** Content Page */
  content,
  /** Forms Page */
  forms,
  /** Media Page */
  media,
});

const grid = document.querySelector('.grid');

const msnry = new Masonry( grid , {
  itemSelector: '.grid-item',
  columnWidth: 320,
});


msnry.once('layoutComplete', () => {
  grid.classList.add('load');
});

jQuery(document).ready(function() {
  routes.loadEvents();
});

window.addEventListener("load", function () {
  new ThreeD();
});

/** Load Events */
jQuery(window).load(function() {
  imagesLoaded( grid , function() {
    msnry.layout();
  });
});
