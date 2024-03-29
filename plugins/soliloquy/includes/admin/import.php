<?php
/**
 * Import class.
 *
 * @since 1.0.0
 *
 * @package Soliloquy
 * @author  Thomas Griffin
 */
 
 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Soliloquy_Import {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds any plugin error messages.
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $errors = array();

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Soliloquy::get_instance();

        // Import a slider.
        $this->import_slider();

        // Add any notices generated by the import script.
        add_action( 'admin_notices', array( $this, 'notices' ) );

    }

    /**
     * Imports a Soliloquy slider.
     *
     * @since 1.0.0
     *
     * @return null Return early (possibly setting errors) if failing proper checks to import the slider.
     */
    public function import_slider() {

        if ( ! $this->has_imported_slider() ) {
            return;
        }

        if ( ! $this->verify_imported_slider() ) {
            return;
        }

        if ( ! $this->can_import_slider() ) {
            $this->errors[] = esc_attr__( 'Sorry, but you lack the permissions to import a slider to this post.', 'soliloquy' );
            return;
        }

        if ( ! $this->post_can_handle_slider() ) {
            $this->errors[] = esc_attr__( 'Sorry, but the post ID you are attempting to import the slider to cannot handle a slider.', 'soliloquy' );
            return;
        }

        if ( ! $this->has_imported_slider_files() ) {
            $this->errors[] = esc_attr__( 'Sorry, but there are no files available to import a slider.', 'soliloquy' );
            return;
        }

        if ( ! $this->has_correct_filename() ) {
            $this->errors[] = esc_attr__( 'Sorry, but you have attempted to upload a slider import file with an incompatible filename. Soliloquy import files must begin with "soliloquy".', 'soliloquy' );
            return;
        }

        if ( ! $this->has_json_extension() ) {
            $this->errors[] = esc_attr__( 'Sorry, but Soliloquy import files must be in <code>.json</code> format.', 'soliloquy' );
            return;
        }

        // Retrieve the JSON contents of the file. If that fails, return an error.
        $contents = $this->get_file_contents();
        if ( ! $contents ) {
            $this->errors[] = esc_attr__( 'Sorry, but there was an error retrieving the contents of the slider export file. Please try again.', 'soliloquy' );
            return;
        }

        // Decode the settings and start processing.
        $data    = json_decode( $contents, true );
        $post_id = absint( $_POST['soliloquy_post_id'] );

        // If the post is an auto-draft (new post), make sure to save as draft first before importing.
        $this->maybe_save_draft( $post_id );

        // Delete any previous slider data (if any) from the post that is receiving the new slider.
        $this->remove_existing_slider( $post_id );

        // Update the ID in the slider data to point to the new post.
        $data['id'] = $post_id;

        // If the wp_generate_attachment_metadata function does not exist, load it into memory because we will need it.
        $this->load_metadata_function();

        // Prepare import.
        $this->prepare_import();

        // Import the slider.
        $slider = $this->run_import( $data, $post_id );

        // Cleanup import.
        $this->cleanup_import();

        // Update the in_slider checker for the post that is receiving the slider.
        update_post_meta( $post_id, '_sol_in_slider', $slider['in_slider'] );

        // Unset any unncessary data from the final slider holder.
        unset( $slider['in_slider'] );
        
        //Filter to ignore imported
		if ( apply_filters( 'soliloquy_imported_title', true ) ){
			
	        // Update the slider title and slug to avoid any confusion if importing on same site.
	        $slider['config']['title'] = sprintf( esc_attr__( 'Imported Slider #%s', 'soliloquy' ), $post_id );
	        $slider['config']['slug']  = 'imported-slider-' . $post_id;
        
        }

        // Update the meta for the post that is receiving the slider.
        update_post_meta( $post_id, '_sol_slider_data', $slider );

    }

    /**
     * Loops through the data provided and imports items into the slider.
     *
     * @since 1.0.0
     *
     * @param array $data    Array of slider data being imported.
     * @param int $post_id   The post ID the slider is being imported to.
     * @return array $slider Modified slider data based on imports.
     */
    public function run_import( $data, $post_id ) {

        // Prepare variables.
        $slider = $data;
        $slider['slider'] = array();

        // Loop through the slider items and import each item individually.
        foreach ( (array) $data['slider'] as $id => $item ) {

            // Store image locally and get its properties
            $image = $this->import_slider_item( $id, $item, $data, $post_id );

            // Replace image in $item
            $item['id'] = $image['attachment_id'];
            $item['src'] = $image['url'];

            // Store in new slider
            $slider['slider'][ $image['attachment_id'] ] = $item;

        }

        // Return the newly imported slider data.
        return $slider;

    }

    /**
     * Imports an individual item into a slider.
     *
     * @since 1.0.0
     *
     * @param int $id       The image attachment ID from the import file.
     * @param array $item   Data for the item being imported.
     * @param array $slider Array of slider data being imported.
     * @param int $post_id  The post ID the slider is being imported to.
     * @return array        New Image src
     */
    public function import_slider_item( $id, $item, $data, $post_id ) {

        // If no image data was found, the image doesn't exist on the server.
        $image = wp_get_attachment_image_src( $id );
        $new_image = array(
            'url'           => '',
            'attachment_id' => 0,
        );

        if ( ! $image ) {
            // We need to stream our image from a remote source.
            if ( empty( $item['src'] ) ) {
                $this->errors[] = esc_attr__( 'No valid URL found for the image ID #' . $id . '.', 'soliloquy' );
            } else {
                // Stream the image from a remote URL.
                $new_image = $this->import_remote_image( $item['src'], $data, $post_id, $id, true );
            }
        } else {
            // The image already exists. If the URLs don't match, stream the image into the slider.
            if ( $image[0] !== $item['src'] ) {
                // Stream the image from a remote URL.
                $new_image = $this->import_remote_image( $item['src'], $data, $post_id, $id, true );
            } else {
                // URLs match. Nothing more to do
                $new_image = array(
                    'url'           => $item['src'],
                    'attachment_id' => $id,
                );
            }
        }

        // Return the imported image details
        return apply_filters( 'soliloquy_imported_image_data', $new_image, $id, $item, $post_id );

    }

    /**
     * Helper method to stream and import an image from a remote URL.
     *
     * @since 1.0.0
     *
     * @param string $url       The URL of the remote image to stream and import.
     * @param array $data       The data to use for importing the remote image.
     * @param int $post_id      The post ID receiving the remote image.
     * @param int $id           The image attachment ID to target (if available).
     * @param bool $stream_only Whether or not to only stream and import or actually add to slider.
     * @return array $data      Data with updated import information.
     */
    public function import_remote_image( $src, $data, $post_id, $id = 0, $stream_only = false ) {
				
        // Prepare variables.
        $new_image = $src;
        $stream    = wp_remote_get( $new_image, array( 'timeout' => 60 ) );
        $type      = wp_remote_retrieve_header( $stream, 'content-type' );
        $filename  = basename( $new_image );
        $fileinfo  = pathinfo( $filename );
               
        // If the filename doesn't have an extension on it, determine the filename to use to save this image to the Media Library
        // This fixes importing URLs with no file extension e.g. http://placehold.it/300x300 (which is a PNG)
        if ( ! isset( $fileinfo['extension'] ) || empty( $fileinfo['extension'] ) ) {
            switch ( $type ) {
                case 'image/jpeg':
                    $filename = $filename . '.jpeg';
                    break;
                case 'image/jpg':
                    $filename = $filename . '.jpg';
                    break;
                case 'image/gif':
                    $filename = $filename . '.gif';
                    break;
                case 'image/png':
                    $filename = $filename . '.png';
                    break;
            }
        }
        
        // If we cannot get the image or determine the type, skip over the image.
        if ( is_wp_error( $stream ) || ! $type ) {
            $this->errors[] = esc_attr__( 'Could not retrieve a valid image from the URL ' . $new_image . '.', 'soliloquy' );

            // Unset it from the slider data for meta saving.
            if ( $id ) {
                $data = $this->purge_image_from_slider( $id, $data );
            }

            // If only streaming, return the error.
            if ( $stream_only ) {
                return apply_filters( 'soliloquy_remote_image_import_only_error', $stream );
            }
        } else {
            // It is an image. Stream the image.
            $mirror = wp_upload_bits( basename( $new_image ), null, wp_remote_retrieve_body( $stream ) );

            // If there is an error, bail.
            if ( ! empty( $mirror['error'] ) ) {
                $this->errors[] = $mirror['error'];

                // Unset it from the slider data for meta saving.
                if ( $id ) {
	                
                    $data = $this->purge_image_from_slider( $id, $data );
                }

                // If only streaming, return the error.
                if ( $stream_only ) {
                    return apply_filters( 'soliloquy_remote_image_import_only_error', $mirror );
                }
            } else {
                $attachment = array(
                    'post_title'     => basename( $new_image ),
                    'post_mime_type' => $type
                );
                $attach_id  = wp_insert_attachment( $attachment, $mirror['file'], $post_id );

                // Generate and update attachment metadata.
                if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
                    require ABSPATH . 'wp-admin/includes/image.php';
                }

                // Generate and update attachment metadata.
                $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );
                wp_update_attachment_metadata( $attach_id, $attach_data );

                // Unset it from the slider data for meta saving now that we have a new image in its place.
                if ( $id ) {	          

                    $data = $this->purge_image_from_slider( $id, $data );
                }
                
                // Assign the attachment ID to $mirror, so we know the Media Library image attachment ID
                $mirror['attachment_id'] = $attach_id;
                
                // If only streaming and importing the image from the remote source, return it now.
                if ( $stream_only ) {
                    return apply_filters( 'soliloquy_remote_image_import_only', $mirror, $attach_data, $attach_id );
                }

                // Add the new attachment ID to the in_slider checker.
                $data['in_slider'][] = $attach_id;

                // Now update the attachment reference checker.
                $this->update_slider_checker( $attach_id, $post_id );

                // Add the new attachment to the slider.
                $data = $this->update_attachment_meta( $data, $attach_id );
            }
        }

        // Return the remote image import data.
        return apply_filters( 'soliloquy_remote_image_import', $data, $src, $id );

    }

    /**
     * Purge image data from a slider.
     *
     * @since 1.0.0
     *
     * @param int $id      The image attachment ID to target for purging.
     * @param array $data  The data to purge.
     * @return array $data Purged data.
     */
    public function purge_image_from_slider( $id, $data ) {

        // Remove the image ID from the slider data.
        unset( $data['slider'][$id] );
        
        if ( isset( $data['in_slider'] ) ) {
	
	        if ( ( $key = array_search( $id, (array) $data['in_slider'] ) ) !== false ) {
	            unset( $data['in_slider'][$key] );
	        }
	        
        }

        // Return the purged data.
        return apply_filters( 'soliloquy_image_purged', $data, $id );

    }

    /**
     * Update the attachment with a reference to the slider that
     * it has been assigned to.
     *
     * @since 1.0.0
     *
     * @param int $attach_id The image attachment ID to target.
     * @param int $post_id   The post ID the attachment should reference.
     */
    public function update_slider_checker( $attach_id, $post_id ) {

        $has_slider = get_post_meta( $attach_id, '_sol_has_slider', true );
        if ( empty( $has_slider ) ) {
            $has_slider = array();
        }

        $has_slider[] = $post_id;
        update_post_meta( $attach_id, '_sol_has_slider', $has_slider );

    }

    /**
     * Update the image metadata for Soliloquy.
     *
     * @since 1.0.0
     *
     * @param array $data    The data to use for importing the remote image.
     * @param int $attach_id The image attachment ID to target.
     * @return array $data   Data with updated meta information.
     */
    public function update_attachment_meta( $data, $attach_id ) {
		
		$ajax = Soliloquy_Ajax::get_instance();
		
        return $ajax->prepare_slider_data( $data, $attach_id );

    }

    /**
     * Determines if a slider import is available.
     *
     * @since 1.0.0
     *
     * @return bool True if an imported slider is available, false otherwise.
     */
    public function has_imported_slider() {

        return ! empty( $_POST['soliloquy_import'] );

    }

    /**
     * Determines if a slider import nonce is valid and verified.
     *
     * @since 1.0.0
     *
     * @return bool True if the nonce is valid, false otherwise.
     */
    public function verify_imported_slider() {

        return isset( $_POST['soliloquy-import'] ) && wp_verify_nonce( $_POST['soliloquy-import'], 'soliloquy-import' );

    }

    /**
     * Determines if the user can actually import the slider.
     *
     * @since 1.0.0
     *
     * @return bool True if the user can import the slider, false otherwise.
     */
    public function can_import_slider() {

        return apply_filters( 'soliloquy_import_cap', current_user_can( 'manage_options' ) );

    }

    /**
     * Determines if the post ID can handle a slider (revision or not).
     *
     * @since 1.0.0
     *
     * @return bool True if the post ID is not a revision, false otherwise.
     */
    public function post_can_handle_slider() {

        return isset( $_POST['soliloquy_post_id'] ) && ! wp_is_post_revision( $_POST['soliloquy_post_id'] );

    }

    /**
     * Determines if slider import files are available.
     *
     * @since 1.0.0
     *
     * @return bool True if the imported slider files are available, false otherwise.
     */
    public function has_imported_slider_files() {

        return ! empty( $_FILES['soliloquy_import_slider']['name'] ) || ! empty( $_FILES['soliloquy_import_slider']['tmp_name'] );

    }

    /**
     * Determines if a slider import file has a proper filename.
     *
     * @since 1.0.0
     *
     * @return bool True if the imported slider file has a proper filename, false otherwise.
     */
    public function has_correct_filename() {

        return preg_match( '#^soliloquy#i', $_FILES['soliloquy_import_slider']['name'] );

    }

    /**
     * Determines if a slider import file has a proper file extension.
     *
     * @since 1.0.0
     *
     * @return bool True if the imported slider file has a proper file extension, false otherwise.
     */
    public function has_json_extension() {

        $file_array = explode( '.', $_FILES['soliloquy_import_slider']['name'] );
        $extension  = end( $file_array );
        return 'json' === $extension;

    }

    /**
     * Retrieve the contents of the imported slider file.
     *
     * @since 1.0.0
     *
     * @return string|bool JSON contents string if successful, false otherwise.
     */
    public function get_file_contents() {

        $file = $_FILES['soliloquy_import_slider']['tmp_name'];
        return @file_get_contents( $file );

    }

    /**
     * Move a new post to draft mode before importing a slider.
     *
     * @since 1.0.0
     *
     * @param int $post_id The current post ID handling the slider import.
     */
    public function maybe_save_draft( $post_id ) {

        $post = get_post( $post_id );
        if ( 'auto-draft' == $post->post_status ) {
            $draft = array(
                'ID'          => $post_id,
                'post_status' => 'draft'
            );
            wp_update_post( $draft );
        }

    }

    /**
     * Helper method to remove existing slider data when a slider is imported.
     *
     * @since 1.0.0
     *
     * @param int $post_id The current post ID handling the slider import.
     */
    public function remove_existing_slider( $post_id ) {

        delete_post_meta( $post_id, '_sol_slider_data' );
        delete_post_meta( $post_id, '_sol_in_slider' );

    }

    /**
     * Load the wp_generate_attachment_metadata function if necessary.
     *
     * @since 1.0.0
     */
    public function load_metadata_function() {

        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

    }

    /**
     * Set timeout to 0 and suspend cache invalidation while importing a slider.
     *
     * @since 1.0.0
     */
    public function prepare_import() {

        set_time_limit( $this->get_max_execution_time() );
        wp_suspend_cache_invalidation( true );

    }

    /**
     * Reset cache invalidation and flush the internal cache after importing a slider.
     *
     * @since 1.0.0
     */
    public function cleanup_import() {

        wp_suspend_cache_invalidation( false );
        wp_cache_flush();

    }

    /**
     * Helper method to return the max execution time for scripts.
     *
     * @since 1.0.0
     *
     * @param int $time The max execution time available for PHP scripts.
     */
    public function get_max_execution_time() {

        $time = ini_get( 'max_execution_time' );
        return ! $time || empty( $time ) ? (int) 0 : $time;

    }

    /**
     * Outputs any errors or notices generated by the class.
     *
     * @since 1.0.0
     */
    public function notices() {

        if ( ! empty( $this->errors ) ) :
        ?>
        <div id="message" class="error">
            <p><?php echo implode( '<br>', $this->errors ); ?></p>
        </div>
        <?php
        endif;

        // If a slider has been imported, create a notice for the import status.
        if ( isset( $_GET['soliloquy-imported'] ) && $_GET['soliloquy-imported'] ) :
        ?>
        <div id="message" class="updated">
            <p><?php esc_html_e( 'Soliloquy slider imported. Please check to ensure all images and data have been imported properly.', 'soliloquy' ); ?></p>
        </div>
        <?php
        endif;

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Soliloquy_Import object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Import ) ) {
            self::$instance = new Soliloquy_Import();
        }

        return self::$instance;

    }

}

// Load the import class.
$soliloquy_import = Soliloquy_Import::get_instance();