// jshint esversion:6

export default {
  init() {
    // JavaScript to be fired on all pages

    (function($){
        $('.slider').slider();

        $('.primary-navigation-li').pushpin({
          top: 233,
          bottom: 100000,
          offset: 32,
        });

        $('.primary-navigation').pushpin({
          top: 233,
          bottom: 100000,
          offset: 0,
        });

        $('.tap-target').tapTarget('open');
        $('.tap-target').tapTarget('close');

        $('.scrollspy').scrollSpy();

        $(".button-collapse").sideNav();

        $('ul.tabs').tabs();

        $('.table-of-contents').pushpin({
          top: 367,
          bottom: 100000,
          offset: 80,
        });

        if ($('.parallax').length) {
          $('.parallax').parallax();
        }

        if ($('#tabs-swipe-demo').length) {
          $('#tabs-swipe-demo').tabs({ 'swipeable': true });
        }

        $(".dropdown-button").dropdown();

        $('.pushpin-demo-nav').each(function() {
          var $this = $(this);
          var $target = $('#' + $(this).attr('data-target'));
          $this.pushpin({
            top: $target.offset().top,
            bottom: $target.offset().top + $target.outerHeight() - $this.height(),
          });
        });


        $('.modal').modal();

        $('.materialboxed').materialbox();

      // CSS Transitions Demo Init

        if ($('#scale-demo').length && $('#scale-demo-trigger').length) {
          $('#scale-demo-trigger').click(function() {
            $('#scale-demo').toggleClass('scale-out');
          });
        }


      // Toggle Flow Text

        var toggleFlowTextButton = $('#flow-toggle');
        toggleFlowTextButton.click( function(){
          $('#flow-text-demo').children('p').each(function(){
              $(this).toggleClass('flow-text');
            });
        });

        $('.carousel').carousel();

        $('.carousel.carousel-slider').carousel({fullWidth: true});

        $('.pgrid').masonry({
          // options
          itemSelector: '.pgrid-item',
          columnWidth: 180,
        });

        // Chips autocomplete
        $('.chips').material_chip();
        $('.chips-initial').material_chip({
          data: [{
            tag: 'Apple',
          }, {
            tag: 'Microsoft',
          }, {
            tag: 'Google',
          }],
        });
        $('.chips-placeholder').material_chip({
          placeholder: 'Enter a tag',
          secondaryPlaceholder: '+Tag',
        });
        $('.chips-autocomplete').material_chip({
          autocompleteOptions: {
            data: {
              'Apple': null,
              'Microsoft': null,
              'Google': null,
            },
            limit: Infinity,
            minLength: 1,
          },
        });


    })(jQuery);
  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
