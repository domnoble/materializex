import noUiSlider from 'materialize-css/extras/noUiSlider/nouislider';

const rangeSlider = document.getElementById('range-input');

export default {
  init() {
    /**
     *
     * Forms Page JavaScript
     *
     **/

    noUiSlider.create(rangeSlider, {
     start: [20, 80],
     connect: true,
     step: 1,
     range: {
       'min': 0,
       'max': 100,
     },
    });

    // JavaScript to be fired on all pages
    (function($){

    // Forms
      $('select').material_select();

      $('.datepicker').pickadate({
         selectMonths: true,
     // Creates a dropdown to control month
         selectYears: 15,
     // Creates a dropdown of 15 years to control year
       });

     // Count characters
       $('input#input_text, textarea#textarea1').characterCounter();

     // Autocomplete
       $('input.autocomplete').autocomplete({
         data: {
           "Apple": null,
           "Microsoft": null,
           "Google": 'https://placehold.it/250x250',
         },
         limit: 20, // The max amount of results that can be shown at once. Default: Infinity.
         onAutocomplete: function() {
           // Callback function when value is autcompleted.
         },
         minLength: 1, // The minimum length of the input for the autocomplete to start. Default: 1.
       });

    })(jQuery);


  },
  finalize() {
    // JavaScript to be fired on all pages, after page specific JS is fired
  },
};
