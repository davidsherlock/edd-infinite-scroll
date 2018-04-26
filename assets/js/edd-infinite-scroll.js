(function($) {
    var container; // Used to initial load check

    var animation_interval; // Running animation interval

    $(window).on('scroll touchstart', function (e) {
        $('.edd-infinite-scrolling').each(function() {
            if( ( $(window).scrollTop() + $(window).height() ) >= ( $(this).offset().top + $(this).height() ) ) {
                $('body').trigger( $.Event('visibleOnScroll', { target: this }) );
            }
        });
    });

    // Infinite scrolling pagination
    $('body').on('visibleOnScroll', '.edd-infinite-scrolling', function (e) {
        var container = $(this);
        var loader = $(this).next('.edd-infinite-scrolling-loader');

        if( ! container.hasClass('edd-infinite-scrolling-loading') && container.find('.edd-infinite-scrolling-end').length == 0 ) {
            // Shortcode atts form
            var shortcode_atts_form = container.siblings('#edd-infinite-scrolling-shortcode-atts');

            // Class to check if this container is loading
            container.addClass('edd-infinite-scrolling-loading');

            // Increments current page
            shortcode_atts_form.find('input[name="paged"]').val( parseInt( shortcode_atts_form.find('input[name="paged"]').val() ) + 1 );

            // Enable ajax loader
            loader.find('.edd-loading').addClass('edd-loading-ajax');

            $.ajax({
                url: edd_infinite_scrolling.ajax_url  + '?action=edd_infinite_scrolling&nonce=' + edd_infinite_scrolling.nonce,
                data: shortcode_atts_form.serialize(),
                cache: false,
                success: function( response ) {
                    // Insert returned html
                    if( ! response.is_end ) {
                        var parsed_response = $( response.html );

                        container.append( parsed_response.filter('.edd_downloads_list').html() );

                        // Animate results if is set
                        if( edd_infinite_scrolling.in_animation != '' ) {
                            container.addClass('edd-infinite-scrolling-animating');

                            animation_interval = setInterval(
                                edd_infinite_scrolling_animate,                 // Function
                                100,                                            // Delay
                                container, edd_infinite_scrolling.in_animation  // Parameters
                            );
                        }
                    } else {
                        // End of infinite scrolling
                        container.append( '<div class="edd-infinite-scrolling-end">' + response.html + '</div>' );
                    }

                    // Reset classes
                    loader.find('.edd-loading').removeClass('edd-loading-ajax');
                    container.removeClass('edd-infinite-scrolling-loading');
                }
            });
        }

        return false;
    });

    // Animate downloads on load if is set
    if( edd_infinite_scrolling.in_animation != '' && $('.edd-infinite-scrolling').length != 0 ) {
        container = $('.edd-infinite-scrolling');
        container.addClass('edd-infinite-scrolling-animating');

        animation_interval = setInterval(
            edd_infinite_scrolling_animate,                                     // Function
            100,                                                                // Delay
            container, edd_infinite_scrolling.in_animation                      // Parameters
        );
    }

    // Apply animation to new loaded downloads
    function edd_infinite_scrolling_animate( container, animation ) {
        var elements = container.find('.edd_download:not(.animated)');

        // All elements has been animated
        if(elements.length == 0) {
            // Remove animating class
            container.removeClass('edd-infinite-scrolling-animating');

            // Clear animation interval
            clearInterval(animation_interval);

            return false;
        }

        // Animate the first element
        elements.first().addClass(animation).addClass('animated');
    }
})(jQuery);
