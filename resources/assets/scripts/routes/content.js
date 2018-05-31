// jshint esversion:6
export default {
  init() {

    (function($){

      /**
       *
       *  Content Page JavaScript
       *
       **/

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
