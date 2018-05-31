(function( $ ) {

    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('.color-picker > .color').wpColorPicker();
    });

    $('.grid').masonry({
      // options
      itemSelector: '.grid-item',
      columnWidth: 200,
    });

})( jQuery );
