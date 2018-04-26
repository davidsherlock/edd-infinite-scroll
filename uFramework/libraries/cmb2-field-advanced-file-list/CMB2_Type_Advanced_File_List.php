<?php
/**
 * CMB advanced_file_list field type
 *
 * @since  2.2.2
 *
 * @category  WordPress_Plugin
 * @package   CMB2
 * @author    WebDevStudios
 * @license   GPL-2.0+
 * @link      http://webdevstudios.com
 */

class CMB2_Type_Advanced_File_List extends CMB2_Type_File_Base {

    public function render( $args = array() ) {
        $meta_value = $this->field->escaped_value();
        $name       = $this->_name();
        $img_size   = $this->field->args( 'preview_size' );
        $query_args = $this->field->args( 'query_args' );

        $labels = array(
            'file_full_view_text' => esc_html( $this->_text( 'file_full_view_text', esc_html__( 'Full View', 'cmb2' ) ) ),
            'file_download_text' => esc_html( $this->_text( 'file_download_text', esc_html__( 'Download', 'cmb2' ) ) ),
            'remove_text' => esc_html( $this->_text( 'remove_text', esc_html__( 'Remove', 'cmb2' ) ) ),
        );

        $output     = '';

        // get an array of image size meta data, fallback to 'thumbnail'
        $img_size_data = parent::get_image_size_data( $img_size, 'thumbnail' );

        $output .= parent::render( array(
            'type'              => 'hidden',
            'class'             => 'cmb2-upload-file cmb2-upload-list',
            'size'              => 45, 'desc'  => '', 'value'  => '',
            'data-previewsize'  => sprintf( '[%d,%d]', $img_size_data['width'], $img_size_data['height'] ),
            'data-sizename'     => $img_size_data['name'],
            'data-queryargs'    => ! empty( $query_args ) ? json_encode( $query_args ) : '',
            'js_dependencies'   => 'media-editor',
        ) );

        $output .= parent::render( array(
            'type'              => 'button',
            'class'             => 'cmb2-upload-button button cmb2-upload-list',
            'value'             => esc_attr( $this->_text( 'add_upload_files_text', esc_html__( 'Add or Upload Files', 'cmb2' ) ) ),
            'name'              => '', 'id'  => '',
        ) );

        $output .= '<ul id="' . $this->_id( '-status' ) . '" class="cmb2-media-status cmb-attach-list cmb-advanced-list">';

        if ( $meta_value && is_array( $meta_value ) ) {

            foreach ( $meta_value as $id => $url ) {

                $id_input = parent::render( array(
                    'type'      => 'hidden',
                    'value'     => $url,
                    'name'      => $name . '[' . $id . ']',
                    'id'        => 'filelist-' . $id,
                    'data-id'   => $id,
                    'desc'      => '',
                    'class'     => false,
                ) );

                $output .= '<li class="cmb2-media-item">';

                ob_start();

                if( strpos($id, 'oembed') !== false ) {
                    $this->render_oembed( $id, $url );
                } else {
                    $this->render_attachment( $id, $url );
                }

                $output .= ob_get_clean();

                $output .= '<p class="cmb2-remove-wrapper">';
                    $output .= '<a href="#" class="cmb2-remove-file-button">' . $labels['remove_text'] . '</a>';
                $output .= '</p>';

                $output .= $id_input;

                $output .= '</li>';
            }
        }

        $output .= '</ul>';

        return $this->rendered( $output );
    }

    public function render_attachment( $id, $url ) {
        $img_size   = $this->field->args( 'preview_size' );
        $labels = array(
            'file_full_view_text' => esc_html( $this->_text( 'file_full_view_text', esc_html__( 'Full View', 'cmb2' ) ) ),
            'file_download_text' => esc_html( $this->_text( 'file_download_text', esc_html__( 'Download', 'cmb2' ) ) ),
            'remove_text' => esc_html( $this->_text( 'remove_text', esc_html__( 'Remove', 'cmb2' ) ) ),
        );

        $mime_type = get_post_mime_type( $id );

        list( $type, $subtype ) = explode( '/', $mime_type );

        ?>
        <div class="attachment-item type-<?php echo $type; ?> subtype-<?php echo $subtype; ?>">

            <div class="item-preview">
                <?php if( $type === 'image' ) : ?>
                    <?php echo wp_get_attachment_image( $id, $img_size ); ?>
                <?php else : ?>
                    <img src="<?php echo wp_mime_type_icon( $id ); ?>" class="icon" draggable="false" alt="" />
                <?php endif; ?>
            </div>

            <div class="item-details">
                <div class="filename"><?php echo CMB2_Utils::get_file_name_from_path( $url ); ?></div>

                <?php if( $type === 'image' ) : ?>
                    <a href="<?php echo $url; ?>"><?php echo $labels['file_full_view_text']; ?></a>
                <?php elseif( $type === 'audio' ) : ?>
                    <?php echo wp_audio_shortcode(array(
                        'src' => $url,
                        'loop' => false,
                    ) ); ?>
                <?php elseif( $type === 'video' ) : ?>
                    <?php echo wp_video_shortcode( array(
                        'src' => $url,
                        'loop' => false,
                    ) ); ?>
                <?php else : ?>
                    <a href="<?php echo $url; ?>" target="_blank" rel="external"><?php echo $labels['file_download_text']; ?></a>
                <?php endif; ?>
            </div>

        </div>
        <?php
    }

    public function render_oembed( $id, $url ) {
        $oembed = _wp_oembed_get_object();

        $data = $oembed->get_data( $url, array() );
        ?>
        <div>
            <div class="embed-item provider-<?php echo strtolower( $data->provider_name ); ?> type-<?php echo $data->type; ?>">

                <div class="item-preview">
                    <img src="<?php echo $data->thumbnail_url; ?>" width="<?php echo $data->thumbnail_width; ?>" height="<?php echo $data->thumbnail_height; ?>">
                </div>

                <div class="item-details">
                    <div class="title"><?php echo $data->title; ?></div>
                    <a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
                </div>

            </div>
        </div>
        <?php
    }

}
