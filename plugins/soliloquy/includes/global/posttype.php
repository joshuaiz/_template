<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Posttype class.
 *
 * @since 1.0.0
 *
 * @package Soliloquy
 * @author  Thomas Griffin
 */
class Soliloquy_Posttype {

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
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Soliloquy::get_instance();

        // Build the labels for the post type.
        $labels = array(
            'name'               => esc_attr__( 'Soliloquy Sliders', 'soliloquy' ),
            'singular_name'      => esc_attr__( 'Soliloquy', 'soliloquy' ),
            'add_new'            => esc_attr__( 'Add New', 'soliloquy' ),
            'add_new_item'       => esc_attr__( 'Add New Soliloquy Slider', 'soliloquy' ),
            'edit_item'          => esc_attr__( 'Edit Soliloquy Slider', 'soliloquy' ),
            'new_item'           => esc_attr__( 'New Soliloquy Slider', 'soliloquy' ),
            'view_item'          => esc_attr__( 'View Soliloquy Slider', 'soliloquy' ),
            'search_items'       => esc_attr__( 'Search Soliloquy Sliders', 'soliloquy' ),
            'not_found'          => esc_attr__( 'No Soliloquy sliders found.', 'soliloquy' ),
            'not_found_in_trash' => esc_attr__( 'No Soliloquy sliders found in trash.', 'soliloquy' ),
            'parent_item_colon'  => '',
            'menu_name'          => esc_attr__( 'Soliloquy', 'soliloquy' )
        );
        $labels = apply_filters( 'soliloquy_post_type_labels', $labels );

        // Build out the post type arguments.
        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'exclude_from_search' => false,
            'show_ui'             => true,
            'show_in_admin_bar'   => false,
            'rewrite'             => false,
            'query_var'           => false,
            'menu_position'       => apply_filters( 'soliloquy_post_type_menu_position', 248 ),
            'menu_icon'           => plugins_url( 'assets/css/images/menu-icon@2x.png', $this->base->file ),
            'supports'            => array( 'title' )
        );
        $args = apply_filters( 'soliloquy_post_type_args', $args );

        // Register the post type with WordPress.
        register_post_type( 'soliloquy', $args );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Soliloquy_Posttype object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Soliloquy_Posttype ) ) {
            self::$instance = new Soliloquy_Posttype();
        }

        return self::$instance;

    }

}

// Load the posttype class.
$soliloquy_posttype = Soliloquy_Posttype::get_instance();