(function( $ ) {
    $('body').on('submit', '.sellcomet-license-form', function(e) {
        e.preventDefault();

        var $this = $(this);
        var submit = $this.find('input[type="submit"]');
        var action = $this.find('input[name="action"]').val(); // activate or deactivate

        if( $this.find('input[type="text"][name$="-license-key"]').val() == '' ) {
            sellcomet_message('Insert a valid license key', 'error', $this);

            return false;
        }

        if( $this.find('.sellcomet-message').length ) {
            $this.find('.sellcomet-message').fadeOut();
        }

        // Disable button, preventing more clicks during ajax request
        submit.prop('disabled', true);

        // Show the spinner
        $this.find('.spinner').addClass('is-active');

        $.ajax({
            url: sellcomet.ajax_url,
            method: 'post',
            data: $this.serialize(),
            cache: false,
            success: function (response) {

                // Re-enable the load more button
                submit.prop('disabled', false);

                // Remove spinner
                $this.find('.spinner').removeClass('is-active');

                if( response !== null && response.license !== undefined ) {
                    var old_action = ( action == 'sellcomet_deactivate_license' ) ? 'deactivate' : 'activate';
                    var new_action = ( action == 'sellcomet_deactivate_license' ) ? 'activate' : 'deactivate';

                    // On success
                    if( response.success == true ) {

                        // If wrong license, notice it
                        if( old_action === 'activate' && response.license !== 'valid' ) {
                            sellcomet_message('Invalid license key', 'error', $this);
                            return;
                        }

                        var license = $this.find('input[name$="-license-key"]').val();

                        // Switch action
                        $this.find('input[name="action"]').val( 'sellcomet_' + new_action + '_license' );

                        // License read only property
                        $this.find('input[name$="-license-key"]').val(license.substring(0, 4) + '*'.repeat(license.length-8) + license.substring(license.length-4, license.length));
                        $this.find('input[name$="-license-key"]').prop('readOnly', ( new_action == 'deactivate' ));

                        // Submit button
                        submit.attr('name', submit.attr('name').replace( '-' + old_action, '-' + new_action ));
                        submit.attr('id', submit.attr('id').replace( '-' + old_action, '-' + new_action ));
                        submit.val(submit.val().replace( sellcomet_capitalize(old_action), sellcomet_capitalize(new_action) ));

                        // Success message
                        sellcomet_message('License ' + old_action  + 'd successfully', 'success', $this);
                    } else {
                        // Error message
                        if( old_action == 'activate' ) {
                            sellcomet_message('Invalid license', 'error', $this);
                        } else {
                            sellcomet_message('Can not deactivate license', 'error', $this);
                        }
                    }
                } else {
                    sellcomet_message( 'Something went wrong!', 'error', $this );
                }
            }
        });
    });

    function sellcomet_capitalize(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function sellcomet_message( text, type, form ) {
        if( form.find('.sellcomet-message').length == 0 ) {
            $('<span class="sellcomet-message" style="display: none;"></span>').insertAfter( form.find('.spinner') );
        }

        form.find('.sellcomet-message')
            .removeClass('error').removeClass('success')
            .addClass(type)
            .html(text).fadeIn();
    }
})( jQuery );
