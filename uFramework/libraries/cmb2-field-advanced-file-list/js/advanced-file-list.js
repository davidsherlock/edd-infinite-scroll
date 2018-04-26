window.CMB2 = window.CMB2 || {};
(function(window, document, $, cmb, undefined) {
    $(document).on('cmb_media_modal_init', function(e, media) {
        if( media.$field.closest('.cmb-row.cmb-type-advanced-file-list').length ) {
            var l10n = window.cmb2_l10;

            media.frames[ media.field ] = wp.media( {
                title: cmb.metabox().find('label[for="' + media.field + '"]').text(),
                library : media.fieldData.queryargs || {},
                button: {
                    text: l10n.strings[ media.isList ? 'upload_files' : 'upload_file' ]
                },
                multiple: media.isList ? 'add' : false,
                frame: 'post'
            } );
        }
    });

    $(document).on('cmb_media_modal_open', function(e, selection, media) {
        // Keeps visible upload media and insert form url
        $('.media-menu .media-menu-item:not(:first-child):not(:last-child)').css({display: 'none'});
    });

    $(document).on('cmb_media_modal_select', function(e, selection, media) {
        if( media.frames[ media.field ].state().id === 'embed' ) {
            // https://www.youtube.com/watch?v=PZGmTTXCw2M
            var embed_url = media.frames[media.field].state().props.get( 'url' );
            var current_id = media.$field.siblings( '.cmb2-media-status').find('input[id^="filelist-oembed-"]').length;

            $.ajax({
                url: wp.media.view.settings.oEmbedProxyUrl,
                data: {
                    url: embed_url,
                    maxwidth: media.frames[media.field].state().props.get( 'width' ),
                    maxheight: media.frames[media.field].state().props.get( 'height' ),
                    _wpnonce: wp.media.view.settings.nonce.wpRestApi
                },
                type: 'GET',
                dataType: 'json',
                context: this
            })
            .done( function( response ) {
                console.log(response);
                console.log(media);
                console.log(media.$field);

                var template = '';

                template += '<li class="cmb2-media-item">' +
                        '<div class="embed-item provider-' + response.provider_name.toLowerCase() + ' type-' + response.type + '">' +

                            '<div class="item-preview">' +
                                '<img src="' + response.thumbnail_url + '" width="' + response.thumbnail_width + '" height="' + response.thumbnail_height + '">' +
                            '</div>' +

                            '<div class="item-details">' +
                                '<div class="title">' + response.title + '</div>' +
                                '<a href="' + embed_url + '" target="_blank" rel="external">' + embed_url + '</a>' +
                            '</div>' +

                        '</div>' +

                        '<p class="cmb2-remove-wrapper">' +
                            '<a href="#" class="cmb2-remove-file-button"></a>' +
                        '</p>' +

                        '<input type="hidden" name="' + media.$field.attr('name') + '[oembed-' + current_id + ']" id="filelist-oembed-' + current_id + '" value="' + embed_url + '" data-id="' + current_id + '"/>' +

                    '</li>';

                media.$field.siblings( '.cmb2-media-status' ).append(template);
            } );
        }
    });

    function advanced_file_list_embed_provider( url ) {
        var hostname;
        //find & remove protocol (http, ftp, etc.) and get hostname

        if (url.indexOf("://") > -1) {
            hostname = url.split('/')[2];
        }
        else {
            hostname = url.split('/')[0];
        }

        //find & remove port number
        hostname = hostname.split(':')[0];
        //find & remove "?"
        hostname = hostname.split('?')[0];

        var split_hostname = hostname.split('.');

        //extracting the root domain here
        return split_hostname[split_hostname.length-2];
    }
})(window, document, jQuery, window.CMB2);